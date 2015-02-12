Summary: NethServer backup config files only
Name: nethserver-backup-config
Version: 1.3.1
Release: 1
License: GPL
Group: System
Source: %{name}-%{version}.tar.gz
BuildArch: noarch
BuildRequires: nethserver-devtools, gettext
Requires: tar
Requires: nethserver-base
AutoReq: no


%description
NethServer fast backup of config files

%prep
%setup

%build
%{makedocs}
perl createlinks

# create events
mkdir -p root/etc/e-smith/events/post-backup-config
mkdir -p root/etc/e-smith/events/pre-backup-config
mkdir -p root/etc/e-smith/events/pre-restore-config
mkdir -p root/etc/e-smith/events/post-restore-config

# relocate perl modules under default perl vendorlib directory:
mkdir -p root%{perl_vendorlib}
mv -v NethServer root%{perl_vendorlib}

for f in `/bin/ls root/usr/share/locale/*/LC_MESSAGES/nethserver-backup.po`; do
   out=`dirname $f`
   /usr/bin/msgfmt $f -o $out/nethserver-backup.mo
   rm -f $f
done

%install
rm -rf $RPM_BUILD_ROOT
(cd root ; find . -depth -print | cpio -dump $RPM_BUILD_ROOT)
rm -f %{name}-%{version}-%{release}-filelist
/sbin/e-smith/genfilelist $RPM_BUILD_ROOT > %{name}-%{version}-%{release}-filelist

%clean
rm -rf $RPM_BUILD_ROOT


%files -f %{name}-%{version}-%{release}-filelist 
%config /etc/backup-config.d/custom.include
%config /etc/backup-config.d/custom.exclude
%defattr(-,root,root)

%changelog
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

