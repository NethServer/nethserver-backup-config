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
use esmith::I18N;
use Sys::Hostname;
use Proc::ProcessTable;

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
    my $config_dir = shift || '';
    my $self = {
        config_dir => $config_dir,
    };

    $self = bless $self, $class;

    return $self;
}

=head2 get_config_dir

Return the configuration directory.

=cut

sub get_config_dir
{
   my $self = shift;
   return $self->{'config_dir'};
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

    exit(1);
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
        # skip empty lines by matching any non-whitespace char
        next unless /\S/;
        $_ =~ s/\s+$//;
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


=head2 is_running

Check if a process is running.
Takes the process name as argument.

Return 1 if process is running, 0 otherwise.

=cut

sub is_running {
    my $self = shift;
    my $target_name = shift || return 0;
    my $full_match = shift || 0;

    my $t = Proc::ProcessTable->new;
    foreach my $p ( @{$t->table} ){
        next if ($p->pid == $$); # skip running process
        if ($full_match) {
            return 1 if ($p->cmndline =~ /$target_name/);
        } else {
            return 1 if ($p->fname eq $target_name);
        }
    }

    return 0;
}


=head1 AUTHOR

Nethsis srl <support@nethesis.it>

=cut

1;
