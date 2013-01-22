#----------------------------------------------------------------------
# Copyright 1999-2007 Mitel Networks Corporation
# This program is free software; you can redistribute it and/or
# modify it under the same terms as Perl itself.
#----------------------------------------------------------------------

package esmith::Backup;

use strict;
use warnings;

use File::Copy;
use Unix::PasswdFile;
use Unix::GroupFile;

use vars qw($VERSION @ISA @EXPORT_OK);

use constant ESMITH_RESTORE_CACHE => '/var/cache/e-smith/restore';
use constant BACKUP_CONFIG_LOG_FILE => "/var/log/backup-config.log";
use constant BACKUP_CONFIG_CONF_DIR => "/etc/backup-config.d/";
use constant BACKUP_CONFIG_DESTINATION => "/tmp/backup-config.tar.gz";
use constant BACKUP_DATA_LOG_FILE => "/var/log/backup-data.log";


@ISA = qw(Exporter);

=head1 NAME

esmith::Backup - interface to server backup/restore information

=head1 SYNOPSIS

    use esmith::Backup;
    my $backup = new esmith::Backup;

=head1 DESCRIPTION

This module provides an abstracted interface to the backup/restore
information

=cut

=head2 new

This is the class constructor.

=cut

sub new
{
    my $class = shift;;
    my $self = {
        _type => shift,
    };
    $self = bless $self, $class;
    if ($self->{_type} eq 'config') {
        $self->{_log_file} = BACKUP_CONFIG_LOG_FILE;
    } elsif ($self->{_type} eq 'data')  {
        $self->{_log_file} = BACKUP_DATA_LOG_FILE;
    } else {
       $self->{_log_file} = '/dev/null';
    }
    return $self;
}


=head2 merge_passwd

Merge password files. Takes a filename of a restored password
file and an optional filename for the final merged password file,
defaulting to /etc/passwd

=item *
Save away the recently restored passwd file

=item *
Put the pre-restore passwd file back in place

=item * 
Add back any users in the restored passwd file with home 
directories under directories which contain user or
machine accounts

=item * 
Log any other missing users or UID/GID mismatches

=cut

my @Scratch_Files = ();

END { unlink @Scratch_Files }

sub merge_passwd
{
    my ($self, $pre_restored, $restored) = @_;

    $restored ||= '/etc/passwd';

    my $tmp = "${restored}.$$";
    push @Scratch_Files, $tmp;
    copy $restored, $tmp or warn "Couldn't copy $restored, $tmp\n";
    copy $pre_restored, $restored or warn "Couldn't copy $pre_restored, $restored\n";

    my $merge_from = new Unix::PasswdFile($tmp, rmode => 'r' );

    unless ($merge_from)
    {
	warn "merge_passwd: Couldn't open restored password object\n";
	return undef;
    }

    my $merge_into = new Unix::PasswdFile($restored);

    unless ($merge_into)
    {
	warn "merge_passwd: Couldn't open current password object\n";
	return undef;
    }

    foreach my $user ($merge_from->users)
    {
	my @details = $merge_into->user($user);

	if ( _homedir_ok($merge_from->home($user)) )
	{
	    unless ( defined $details[0] )
	    {
		$merge_into->user($user, $merge_from->user($user));
		warn "merge_passwd: Restoring user $user\n";
	    }

	    next;
	}

	unless ( defined $details[0] )
	{
	    warn "merge_passwd: $user - Missing after restore\n";
	    next;
	}

	unless ( $merge_into->uid($user) eq $merge_from->uid($user) )
	{
	    warn "merge_passwd: $user - UID changed during restore\n";
	    next;
	}

	unless ( $merge_into->gid($user) eq $merge_from->gid($user) )
	{
	    warn "merge_passwd: $user - GID changed during restore\n";
	    next;
	}
    }

    $merge_into->commit;

    return 1;
}

=head2 merge_group

Merge group files. Takes a filename of a restored group
file and an optional filename for the final merged group file,
defaulting to /etc/group.

=item *
Save away the recently restored group file

=item *
Put the pre-restore group file back in place

=item *
Add back any group in the restored group file for which
there are corresponding users with valid home directories.
These users are checked from the passwd file specified in the environment 
variable ESMITH_BACKUP_PASSWD_FILE, or /etc/passwd.

=item *
Log any other missing groups or GID mismatches

=item *
Adjust www, admin, shared groups

=cut

sub merge_group
{
    my ($self, $pre_restored, $restored) = @_;

    $restored ||= '/etc/group';

    my $tmp = "${restored}.$$";
    push @Scratch_Files, $tmp;
    copy $restored, $tmp or warn "Couldn't copy $restored, $tmp\n";
    copy $pre_restored, $restored or warn "Couldn't copy $pre_restored, $restored\n";

    my $merge_from = new Unix::GroupFile($tmp, rmode => 'r' );

    unless ($merge_from)
    {
	warn "merge_group: Couldn't open restored group object\n";
	return undef;
    }

    my $merge_into = new Unix::GroupFile($restored);

    unless ($merge_into)
    {
	warn "merge_group: Couldn't open current group object\n";
	return undef;
    }

    my $passwd_file = $ENV{ESMITH_BACKUP_PASSWD_FILE} || '/etc/passwd';

    my $passwd = new Unix::PasswdFile($passwd_file, rmode => 'r' );

    unless ($passwd)
    {
	warn "merge_group: Couldn't open password object\n";
	return undef;
    }

    foreach my $group ($merge_from->groups)
    {
	my @details = $merge_into->group($group);

	if ( $passwd->user($group) and _homedir_ok($passwd->home($group)) )
	{
	    unless ( defined $details[0] )
	    {
		$merge_into->group($group, $merge_from->group($group));
		warn "merge_group: Restoring group $group\n";
	    }

	    next;
	}

	unless ( defined $details[0] )
	{
	    warn "merge_group: $group - Missing after restore\n";
	    next;
	}

	unless ( $merge_into->gid($group) eq $merge_from->gid($group) )
	{
	    warn "merge_group: $group - GID changed during restore\n";
	    next;
	}
    }

    foreach my $special_group ( qw(admin www shared) )
    {
	$merge_into->group($special_group, $merge_from->group($special_group));
    }

    $merge_into->commit;

    return 1;
}

=head2 save_system_files

Save away system files which get cobbered by a restore

=cut

sub save_system_files
{
    my ($self) = @_;

    my $return = 1;

    unless (chdir ESMITH_RESTORE_CACHE)
    {
	warn "Couldn't change to cache directory\n";
	return undef;
    }

    foreach my $file ( $self->restore_list )
    {
        if ( -f "/$file" )
        {
            unless (copy "/$file", "./$file")
	    {
		warn "Couldn't copy /$file to ./$file\n";
		$return = undef;
	    }
        }
    }

    return $return;
}

=head2 merge_system_files

Merge restored system files with ones on the system

=cut

sub merge_system_files
{
    my ($self) = @_;

    unless (chdir ESMITH_RESTORE_CACHE)
    {
	warn "Couldn't change to cache directory\n";
	return undef;
    }

    if ( -f "./etc/passwd" and -f "/etc/passwd" )
    {
        $self->merge_passwd( "./etc/passwd", "/etc/passwd" );
    }
    else
    {
        warn "Skipping password file merge\n";
    }

    if ( -f "./etc/group" and -f "/etc/group" )
    {
        $self->merge_group( "./etc/group", "/etc/group" );
    }
    else
    {
        warn "Skipping group file merge\n";
    }

    my $now = time();

    foreach my $file ( $self->restore_list )
    {
	if ( -f "./$file" )
	{
	    warn "Preserving $file as $file.$now\n";

	    rename "./$file", "./$file.$now"
		or warn "Couldn't rename ./$file, ./$file.$now\n";
	}
    }

    return 1;
}


=head2 _homedir_ok

Returns true if the given directory is one we want to
restore: /home/e-smith for user accounts or
/noexistingpath for machine accounts

=cut

sub _homedir_ok
{
    my $dir = shift or return;

    return $dir =~ m:^/(home/e-smith|noexistingpath): ;
}




sub logger
{
    use POSIX qw/strftime/;
    my ($self, $tag, $message) = @_;
    open(FILE, ">>".$self->{_log_file});
    print FILE strftime('%D %T',localtime)." - $tag - $message\n";
    close(FILE);
}

sub uniq {
    return keys %{{ map { $_ => 1 } @_ }};
}

sub bad_exit
{
    my ($self, $msg, $status) = @_;

    $msg.= " - $status" unless !defined($status);
    $self->logger('ERROR',$msg);

    exit(1);
}

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


sub backup_config
{
   my ($self, $include_files, $exclude_files) = @_;
   my $fh = File::Temp->new( UNLINK => 0);
   print $fh join("\n",@{$exclude_files});
   my $cmd = "/bin/tar -cpzf /tmp/backup-config.tgz -X ".$fh->filename." ".join(" ",@{$include_files})." 2>/dev/null";
   my $ret = system($cmd);
   if ($ret != 0) {
     $self->bad_exit("ERROR","Can't create tar file",$ret>>8);
   }
}


=head1 AUTHOR

SME Server Developers <bugs@e-smith.com>

=head1 SEE ALSO

=cut

1;
