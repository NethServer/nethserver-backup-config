Summary: NethServer backup config files only
Name: nethserver-backup-config
Version: 2.5.2
Release: 1%{?dist}
License: GPL
Source: %{name}-%{version}.tar.gz
URL: %{url_prefix}/%{name}

BuildArch: noarch
BuildRequires: nethserver-devtools
Requires: tar
Requires: perl-Proc-ProcessTable
Requires: nethserver-base

%description
NethServer fast backup of config files

%prep
%setup

%build
%{makedocs}
perl createlinks

# create events
mkdir -p root/%{_nseventsdir}/post-backup-config
mkdir -p root/%{_nseventsdir}/pre-backup-config
mkdir -p root/%{_nseventsdir}/pre-restore-config
mkdir -p root/%{_nseventsdir}/post-restore-config

# relocate perl modules under default perl vendorlib directory:
mkdir -p root%{perl_vendorlib}
mv -v NethServer root%{perl_vendorlib}

%install
rm -rf %{buildroot}
(cd root ; find . -depth -print | cpio -dump %{buildroot})
%{genfilelist} %{buildroot} > %{name}-%{version}-%{release}-filelist
mkdir -p %{buildroot}/%{_nsstatedir}/backup/history

%pre
# ensure srvmgr user exists:
# configuration backups must be owned by srvmgr user otherwise
# httpd-admin will not be able to deleted them
if ! id srvmgr >/dev/null 2>&1 ; then
   useradd -r -U -G adm srvmgr
fi

%files -f %{name}-%{version}-%{release}-filelist 
%defattr(-,root,root)
%config /etc/backup-config.d/custom.include
%config /etc/backup-config.d/custom.exclude
%doc COPYING
%dir %{_nseventsdir}/%{name}-update
%dir %{_nsstatedir}/backup
%dir %{_nsstatedir}/backup/history
%config %attr(440,root,root) %{_sysconfdir}/sudoers.d/20_nethserver_backup_config

%changelog
* Mon Jun 14 2021 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.5.2-1
- Selective file restore not available after rsync-upgrade - Bug NethServer/dev#6522

* Wed Dec 23 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.5.1-1
- Missing srvmgr user prevents OpenVPN file upload and cause wrong backup config history file owner - Bug NethServer/dev#6375

* Tue Apr 07 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.5.0-1
- Restore configuration without network override - NethServer/dev#6099

* Thu Nov 21 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.4.1-1
- Improve configuration restore - NethServer/dev#5907

* Tue Jul 30 2019 Davide Principi <davide.principi@nethesis.it> - 2.4.0-1
- RPMs cache for config restore procedure - NethServer/dev#5794

* Mon Apr 29 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.3.1-1
- Backup-config history not available after rsync upgrade from 6 to 7 - Bug NethServer/dev#5747

* Wed Jan 30 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.3.0-1
- Remove single backup data - NethServer/dev#5691

* Tue Dec 11 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.2.2-1
- Cockpit Alpha 1 - NethServer/dev#5660

* Fri Sep 28 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.2.1-1
- Backup data fails if include files don't contain a new line  - Bug NethServer/dev#5590

* Tue Aug 28 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.2.0-1
- Backup-data: multiple schedule and backends - NethServer/dev#5538

* Thu May 17 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.1.0-1
- Change of defaults for NS 7.5 - NethServer/dev#5490

* Wed Nov 29 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.0.4-1
- Read-only filesystem after 6.9 restore on 7.4 - Bug NethServer/dev#5388

* Fri Oct 06 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.0.3-1
- Prevent RPM db corruption - NethServer/dev#5354

* Fri Sep 08 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.0.2-1
- CSRF and XSS vulnerabilities in server manager - Bug NethServer/dev#5345
- NS 6 upgrade: avoid restore-data when possible - NethServer/dev#5343

* Mon Jul 24 2017 Davide Principi <davide.principi@nethesis.it> - 2.0.1-1
- Do not try to reinstall existing dependencies -- #22

* Wed Jul 12 2017 Davide Principi <davide.principi@nethesis.it> - 2.0.0-1
- Backup config history - NethServer/dev#5314

* Wed Jun 07 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.5.6-1
- Backup data: duplicity fails with "FilePrefixError: +" - Bug NethServer/dev#5309

* Thu May 25 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.5.5-1
- Add rsync-migrate and rsync-upgrade scripts

* Wed May 10 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.5.4-1
- Upgrade from NS 6 via backup and restore - NethServer/dev#5234

* Thu Apr 20 2017 Davide Principi <davide.principi@nethesis.it> - 1.5.3-1
- Upgrade from NS 6 via backup and restore - NethServer/dev#5234

* Mon Jan 16 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.5.2-1
- DC: restore configuration fails - Bug NethServer/dev#5188

* Thu Aug 25 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.5.1-1
- Package install after restore log noise - Bug NethServer/dev#5081

* Thu Jul 07 2016 Stefano Fancello <stefano.fancello@nethesis.it> - 1.5.0-1
- First NS7 release

* Thu Aug 06 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.2-1
- Configuration backup: support restore of installed RPM - Enhancement #3235 [NethServer]

* Wed Jul 15 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.1-1
- Backup notification: add sender field - Enhancement #3219 [NethServer]
- Add hostname and domain in subject of backup result email - Enhancement #3117 [NethServer]

* Tue Mar 03 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.0-1
- Restore from backup, disaster recovery and network interfaces - Feature #3041 [NethServer]
- nethserver-devbox replacements - Feature #3009 [NethServer]

* Wed Jan 14 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.1-1.ns6
- Configuration backup: missing inline help - Bug #2982 [NethServer]

* Wed Oct 15 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.0-1.ns6
- Backup config: add web interface - Feature #2739

* Tue Aug 05 2014 Davide Principi <davide.principi@nethesis.it> - 1.2.0-1.ns6
- Backup data: avoid multiple sessions - Enhancement #2828 [NethServer]

* Fri Jun 06 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.0-1.ns6
- Backup config: minimize creation of new backup - Enhancement #2699

* Wed Oct 16 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.5-1.ns6
- nethserver-backup-config-network-reset: use update-networks-db script

* Wed Jul 31 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.4-1.ns6
- post-restore-config event: reset network #2043
- Accept '*' character in include and exclude files

* Fri Jul 12 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.3-1.ns6
- Backup: implement and document full restore #2043

* Mon Jun 17 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.2-1.ns6
- Avoid duplicate notifications. Refs #2023
- Implement simple retention policy. Refs #2024

* Tue Apr 30 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.1-1.ns6
- Rebuild for automatic package handling. #1870
- Add mail notification #1672 #1659

* Mon Mar 18 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.0-1
- First release

* Wed Jan 30 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 0.9.0-1
- Fist testing release
