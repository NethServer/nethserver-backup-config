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
use File::Temp;
use NethServer::Backup;

use vars qw($VERSION @ISA @EXPORT_OK);

my $filedate=strftime("_%y%M%d_%H%M%S", localtime($stat->mtime));

use constant LOG_FILE => "/var/log/backup-config.log";
use constant NOTIFICATION_FILE => "/tmp/backup-config-notification";
use constant CONF_DIR => "/etc/backup-config.d/";
use constant DESTINATION => "/var/lib/nethserver/backup/backup-config".$filedate.".tar.xz";


@ISA = qw(NethServer::Backup);

=head1 NAME

NethServer::Backup - interface backup/restore of configuration

=head1 SYNOPSIS

    use NethServer::BackupConfig;
    my $backup = new NethServer::BackupConfig();


=head1 DESCRIPTION

This module provides an interface to the backup/restore of configuration

=cut

=head2 new

This is the class constructor which sets the log file.

=cut

sub new
{
    my $class = shift;
    my $notify = shift || 'error';
    my $notify_to = shift || 'root@localhost';
    my $self = {
        _log_file => LOG_FILE,
        _notify => $notify,
        _notify_to => $notify_to,
        _notification_file => NOTIFICATION_FILE,
    };
    $self = bless $self, $class;
    $self->{_log_lines} = $self->_count_log_lines();
    return $self;
}



=head2 backup_config

Takes two array: a list of files to be included and a list of files to be excluded.
Create an archive (tar.xz) file in BACKUP_CONFIG_DESTINATION.

=cut

sub backup_config
{
   my ($self, $include_files, $exclude_files) = @_;
   my $fh = File::Temp->new( UNLINK => 1);
   print $fh join("\n",@{$exclude_files});
   my $fhi = File::Temp->new( UNLINK => 1);
   print $fhi join("\n",@{$include_files});
   my $cmd = "/bin/tar cpJf ".DESTINATION." -X ".$fh->filename." -T ".$fhi->filename." 2>/dev/null";
   my $return = system($cmd);
   return 0 unless ($return > 0);
   $return = $return>>8;
   if ($return == 2) { #ignore non-existing file errors
       return 0;
   }
   $self->logger("ERROR", "Command was: $cmd");
   return $return;
}


=head1 AUTHOR

Nethsis srl <support@nethesis.it>

=cut

1;
