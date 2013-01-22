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

package NethServer::BackupConfig;

use strict;
use warnings;

use vars qw($VERSION @ISA @EXPORT_OK);

use constant LOG_FILE => "/var/log/backup-config.log";
use constant CONF_DIR => "/etc/backup-config.d/";
use constant DESTINATION => "/tmp/backup-config.tar.gz";


@ISA = qw(Exporter);

=head1 NAME

NethServer::Backup - interface to server backup/restore information

=head1 SYNOPSIS

    use NethServer::BackupConfig;
    my $backup = new NethServer::BackupConfig();


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
    my $self = {};
    $self = bless $self, $class;
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
    open(FILE, ">>".LOG_FILE);
    print FILE strftime('%D %T',localtime)." - $tag - $message\n";
    close(FILE);
}

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
    my ($self, $msg, $status) = @_;

    $msg.= " - ".($status>>8) unless !defined($status);
    $self->logger('ERROR',$msg);

    exit(1) unless  !defined($status);
    exit($status>>8);
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


sub includes
{
    my ($self, $dir) = @_;
    return uniq($self->load_files_from_dir($dir,'include'));
}

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
    my $cmd = `/bin/find $paths $opts -daystart -mtime -2 -type f -print | wc -l`;
    chomp $cmd;

    # return true if there at least one modified files
    return ($cmd gt 0);
}


=head2 backup_config

Takes two array: a list of files to be included and a list of files to be excluded.
Create a tar file in BACKUP_CONFIG_DESTINATION.

=cut

sub backup_config
{
   my ($self, $include_files, $exclude_files) = @_;
   my $fh = File::Temp->new( UNLINK => 0);
   print $fh join("\n",@{$exclude_files});
   my $cmd = "/bin/tar -cpzf ".DESTINATION." -X ".$fh->filename." ".join(" ",@{$include_files})." 2>/dev/null";
   my $ret = system($cmd);
   if ($ret != 0) {
     $self->bad_exit("ERROR","Can't create tar file",$ret);
   }
   return 0;
}


=head1 AUTHOR

Nethsis srl <support@nethesis.it>

=cut

1;
