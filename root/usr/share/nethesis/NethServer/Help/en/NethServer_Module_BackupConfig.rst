======================
Backup (configuration)
======================

Configuration backup contains only the system configuration state. It is designed 
to require little storage and restored quickly.

Create backup
=============

Create a snapshot of the current system configuration state and save it.

Upload
======

Upload an archive.

Configure
=========

Every day a scheduled task adds a snapshot to the list if the configuration has
been changed in the last day. The :guilabel:`Automatic backups to keep` slider
sets how many scheduled backups to keep.

Restore
=======

Restore the configuration backup item. If the option :guilabel:`Download modules
automatically` is checked, additional modules are downloaded and installed
automatically.






