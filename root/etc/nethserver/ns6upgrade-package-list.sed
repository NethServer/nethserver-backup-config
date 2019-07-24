#
# sedscript -- replace/delete ns6 package names for the restore-config procedure
#
s/^nethserver-vpn$/nethserver-openvpn\nnethserver-ipsec-tunnels/
s/^nethserver-collectd-web$/nethserver-cgp/
s/^nethserver-fetchmail$/nethserver-getmail/
s/^nethserver-snort$/nethserver-suricata/
s/^nethserver-ibays$/nethserver-virtualhosts\nnethserver-samba/
/^nethserver-c-icap$/d
/^nethserver-ipsec$/d
