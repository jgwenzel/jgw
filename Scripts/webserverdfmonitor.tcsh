#!/bin/tcsh
##2/16/22 UPDATE- NOW johngwenzel@gmail.com
##This script may be 15-20 years old and was written for a mac.
##It may work in Linux. If so, change the shebang and ext to .sh
##End of UPDATE

##requires user has ssh key for automatic login

set myfile = "/Users/johnwenzel/Documents/WEB/backup/webserver_df.txt"
echo "Disk Free Space for YOUR-WEBSERVER.com webserver <ip address>" > "$myfile" 
date >> "$myfile" 
ssh -c blowfish user@url.com "df -k" | awk '{print $6, $5}' | egrep '(\/ |\/Volumes)' >> "$myfile" 
open $myfile


