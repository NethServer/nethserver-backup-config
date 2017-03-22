Upgrade NS6 using rsync
=======================

This procedure can be used to upgrade a running NethServer 6 to a new server running NethServer 7.
The process is much faster than a traditional backup and restore, also it minimizes the downtime for the users.

What you need

- A running NethServer 6 installation, we will call it original server or source server
- A running NethServer 7 installation with at least the same disk space of the source server, we will call it destination server
- Both machines must be network connected.

Perliminar steps
----------------

Install NethServer 7 on a new machine, make sure you can reach via SSH the original server.
Please make sure the source server allows root login via SSH key and password.

Sync files
----------

The synchronization script will generate a pair of SSH keys, copy the public key to the source server and start the copy of data using rsync. 
The SSH key pair will be destroyed at the end of process.
All directories excluded from the backup data will not be synced.

On the target machine, execute the following command: ::

  screen rsync_upgrade <source_server_name> [ssh_port]

Where

- source_server_name is the host name or IP of the original server
- ssh_port is the ssh port of the original server (default is 22)

Example: ::

    screen rync_upgrade mail.nethserver.org 2222

When asked, insert the root password of the source server, make a coffee and wait patiently.

File synchronization only
^^^^^^^^^^^^^^^^^^^^^^^^^

If the script is invoked with ``-s`` option, no events will be executed on the remote machines.
With this option, the script can be run multiple times.


Example: ::

    screen rync_upgrade -s mail.nethserver.org 2222

Finishing up
------------

At the end of the sync process, if you're ready to migrate to the new machine, stop all services on the source machine
and proceed with the final steps: ::

    signal-event restore-config
    signal-event post-restore-data

