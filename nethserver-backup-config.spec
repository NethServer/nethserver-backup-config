Summary: NethServer backup config files only
Name: nethserver-backup-config
Version: 1.5.2
Release: 1%{?dist}
License: GPL
Source: %{name}-%{version}.tar.gz
URL: %{url_prefix}/%{name}

BuildArch: noarch
BuildRequires: nethserver-devtools
Requires: tar
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

%files -f %{name}-%{version}-%{release}-filelist 
%defattr(-,root,root)
%config /etc/backup-config.d/custom.include
%config /etc/backup-config.d/custom.exclude
%doc COPYING
%doc rsync_upgrade
%dir %{_nseventsdir}/%{name}-update


%changelog
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

