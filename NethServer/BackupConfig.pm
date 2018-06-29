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

@ISA = qw(NethServer::Backup);

=head1 NAME

NethServer::Backup - interface backup/restore of configuration

=head1 SYNOPSIS

    use NethServer::BackupConfig;
    my $backup = new NethServer::BackupConfig();
    print $backup->conf_dir;
    print $backup->destination;


=head1 DESCRIPTION

This module provides an interface to the backup/restore of configuration

=cut

=head2 new

This is the class constructor which sets the configuration directory and destination.

=cut

sub new
{
    my $class = shift;
    my $self = {
        config_dir => "/etc/backup-config.d/",
        destination => "/var/lib/nethserver/backup/backup-config.tar.xz"
    };
    $self = bless $self, $class;
    return $self;
}



=head2 backup_config

Takes two array: a list of files to be included and a list of files to be excluded.
Create an archive (tar.xz) file in destination.

=cut

sub backup_config
{
   my ($self, $include_files, $exclude_files) = @_;
   my $fh = File::Temp->new( UNLINK => 1);
   print $fh join("\n",@{$exclude_files});
   my $fhi = File::Temp->new( UNLINK => 1);
   print $fhi join("\n",@{$include_files});
   my $cmd = "/bin/tar cpJf ".$self->{'destination'}." -X ".$fh->filename." -T ".$fhi->filename." 2>/dev/null";
   my $return = system($cmd);
   return 0 unless ($return > 0);
   $return = $return>>8;
   if ($return == 2) { #ignore non-existing file errors
       return 0;
   }
   return $return;
}



=head2 get_destination

Return the destination file name.

=cut

sub get_destination
{
   my $self = shift;
   return $self->{'destination'};
}


=head1 AUTHOR

Nethsis srl <support@nethesis.it>

=cut

1;
