#
# Copyright (C) 2013 Nethesis S.r.l.
# http://www.nethesis.it - support@nethesis.it
# 
# This script is part of NethServer.
# 
# NethServer is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License,
# or any later version.
# 
# NethServer is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
#

package NethServer::Backup;

use strict;
use warnings;
use Locale::gettext;
use esmith::I18N;
use Sys::Hostname;

use vars qw($VERSION @ISA @EXPORT_OK);

=head1 NAME

NethServer::Backup - interface to server backup/restore information

=head1 SYNOPSIS

    Do not use this class directly.
    Use NethServer::BackupConfig() or NethServer::BackupData();
    
=head1 DESCRIPTION

This module provides an abstracted interface to the backup/restore
information

=cut

=head2 new

This is the class constructor.

=cut

sub new
{
    my $class = shift;
    my $notify = shift || 'never';
    my $notify_to = shift || '';
    my $self = {
        _notify => $notify,
        _notify_to => $notify_to,
    };
    $self = bless $self, $class;

    my $i18n = new esmith::I18N;
    $i18n->setLocale("nethserver-backup");

    return $self;
}



=head2 logger

Take a tag and a message.
Write a log line to  _log_file file.
Each line is in the form: DATE HOUR - tag - message

=cut

sub logger
{
    use POSIX qw/strftime/;
    my ($self, $tag, $message) = @_;
    return unless defined($self->{_log_file});
    open(FILE, ">>".$self->{_log_file});
    print FILE strftime('%F %T',localtime)." - $tag - $message\n";
    close(FILE);
}

=head2 logger

Take a message.
Write a localized notification line to  _notification_file file.

=cut

sub notify
{
    use POSIX qw/strftime/;
    my ($self, $message) = @_;
    shift @_;
    shift @_;
    return unless ($self->{_notify} ne 'never');
    return unless ($self->{_notify_to} ne '');
    return unless defined($self->{_notification_file});

    my $i18n = new esmith::I18N;
    $i18n->setLocale("nethserver-backup");
   
    open(FILE, ">>".$self->{_notification_file});
    print FILE sprintf(gettext($message)."\n",@_);
    close(FILE);
}


=head2 uniq

Remove all duplicates from the given array.

=cut

sub uniq {
    return keys %{{ map { $_ => 1 } @_ }};
}

=head2 bad_exit

Take a message and an optional status.
Print the given message to the log prepending 'ERROR' tag. 
Exit 1 or with the given status.
If status is defined, add the status to the log.

=cut

sub bad_exit
{
    my ($self, $msg, $status, $log) = @_;
    $msg.= " - ".$status unless !defined($status);
    $self->logger('ERROR',$msg);

    if ($self->{_notify_to} ne '') { #avoid notification if not directlry requested
        if ( ($self->{_notify} eq "error") or ($self->{_notify} eq "always") ) {
            $self->_send_notification(1,$log);
        }
        unlink $self->{_notification_file};
    }

    exit(1) unless defined($status);
    exit($status);
}


=head2 cleanup_notification

Remove existing notification file.

=cut

sub cleanup_notification
{
    my ($self) = @_;
    unlink $self->{_notification_file};
}

=head2 send_notification

Send notification if notify type is 'always'.

=cut

sub send_notification
{
    my ($self, $is_error, $log) = @_;
    if ($self->{_notify} eq "always")  {
        $self->_send_notification($is_error, $log);
    }
}

sub _send_notification
{
    my ($self, $is_error, $log) = @_;
    my $content;
    my $status;

    return unless(-f $self->{_notification_file});

    open(my $fh, '<', $self->{_notification_file}) or $self->logger("NOTIFY","Can't read notification file");
    {
        local $/;
        $content = <$fh>;
    }
    close($fh);

    $content .= "\n\n\n".sprintf(gettext('Extract from log file %s'),$self->{_log_file}).":\n\n";
    $content .= $self->_extract_log();
    $content .= "\n";
    if (defined($log) && -f $log) {
        $content .= "\n\n\n".sprintf(gettext('Extract from log file %s'),$log).":\n\n";
        open (FILE, $log);
        while (<FILE>) {
            $content.=$_;
        }
        close FILE;
        $content .= "\n";
    }
   
    my $i18n = new esmith::I18N;
    $i18n->setLocale("nethserver-backup");
    
    if ($is_error) {
        $status = ": ".gettext("ERROR");
    } else {
        $status = ": ".gettext("SUCCESS");
    }

    my $host = hostname;
    open(MAIL, "|/usr/sbin/sendmail -t");
    print MAIL "To: ".$self->{_notify_to}."\n";
    print MAIL "From: Backup <admin@".$host.">\n";
    print MAIL "Subject: ".gettext('Backup report').$status."\n\n";
    print MAIL $content;
    close(MAIL);

    unlink $self->{_notification_file};
}

=head2 load_file_list

Given a file name, return all lines in an array.

=cut

sub load_file_list
{
    my ($self, $file) = @_;
    my @paths;
    open (FILE, $file) or die 'Unable to open the list file: $file';

    while (<FILE>) {
        chop($_);
        next if (/.*\*.*/);
        push(@paths, $_);
    }
    close(FILE);

   return @paths;
}


=head2 load_files_from_dir

Given a directory and an extension, return all lines
from all files using load_file_list function.

=cut
sub load_files_from_dir
{
    my ($self, $dir, $extension ) = @_;
    my @ret;
    my @files = <$dir*.$extension>;
    foreach my $file (@files) {
       push(@ret,$self->load_file_list($file));
    }
    return @ret;
}

=head2 includes
 
 Takes a directory as argument.
 Returns a list of files from all .include files inside the given directory.
 All duplicates are removed.

=cut

sub includes
{
    my ($self, $dir) = @_;
    return uniq($self->load_files_from_dir($dir,'include'));
}

=head2 excludes
 
 Takes a directory as argument.
 Returns a list of files from all .exclude files inside the given directory.
 All duplicates are removed.

=cut

sub excludes
{
    my ($self, $dir) = @_;
    return uniq($self->load_files_from_dir($dir,'exclude'));
}


=head2 changed

Takes two array: a list of files to be included and a list of files to be excluded.
Using the find command, search all included files and directories and return 1
if any file is changed in the last 24 hours, 0 otherwise. 
List of excluded files is not include in the search.

=cut

sub changed
{
    my ($self, $include_files, $exclude_files) = @_;
    my $opts = '';
    my $paths = join(" ",@{$include_files});
    $opts = join(" -path ", @{$exclude_files});

    if ($opts ne '') { # add exclusions
       $opts = " \\( -path $opts \\) -prune -o ";
    }

    # count how many files have been modified in the last 24 hours
    my $cmd = `/bin/find $paths $opts -daystart -mtime -2 -type f -print 2>/dev/null | wc -l`;
    chomp $cmd;

    # return true if there at least one modified files
    return ($cmd gt 0);
}


=head2 is_mounted

Takes a directory or a regexp.
Return true if the direcotry is mounted, false otherwise.

=cut

sub is_mounted
{
    my ($self, $dir) = @_;
    my $err = 0;
    open FD, '/proc/mounts';
    while (<FD>)
    {
        next unless /$dir/;
        $err++;
    }
    close FD;
    return ($err != 0);
}


=head2 get_log_file

Returns the current log file name.

=cut

sub get_log_file 
{
    my $self = shift;
    return $self->{_log_file};
}

=head2 set_log_file

Takes a file name.
Set the current log file name.

=cut

sub set_log_file 
{
    my $self = shift;
    my $file = shift;
    $self->{_log_file} = $file;
}

=head2 get_notification_file

Returns the current notification file name.

=cut

sub get_notification_file 
{
    my $self = shift;
    return $self->{_notification_file};
}

=head2 set_notification_file

Takes a file name.
Set the current notification file name.

=cut

sub set_notification_file 
{
    my $self = shift;
    my $file = shift;
    $self->{_notification_file} = $file;
}

=head2 count_log_lines

Count lines of log file

=cut

sub _count_log_lines 
{
    my $self = shift;
    my $lines = 0;
    open (FILE, $self->{_log_file}) or return 0;
    $lines++ while (<FILE>);
    close FILE;
    return $lines;
}


=head2 extract_log

Return log file content starting from line _log_lines

=cut

sub _extract_log
{
    my $self = shift;
    my $ret = "";
    my $lines = 0;
    open (FILE, $self->{_log_file}) or return "";
    while (<FILE>) {
        $lines++;
        if($lines > $self->{_log_lines}) {
            $ret.=$_;
        }
    }
    close FILE;
    return $ret;
}
=head1 AUTHOR

Nethsis srl <support@nethesis.it>

=cut

1;
