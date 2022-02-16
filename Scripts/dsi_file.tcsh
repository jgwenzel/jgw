#!/bin/tcsh
##2/16/22 UPDATE- NOW johngwenzel@gmail.com
##This script may be 15-20 years old and was written for a mac.
##It may work in Linux. If so, change the shebang and ext to .sh
##End of UPDATE

##This script took the CSV I was mailed daily and uploaded to our webserver for processing

cp /Users/jwenzel/Library/Mail/POP-john.wenzeldata.com@integraonline.com/INBOX.mbox/Your_Daily_DSI_Prici.mimeattach/filename.csv /dsi-filename.csv

cd /

chmod u=rwx,g=rwx,o=rwx dsi-filename.csv

ftp -a -u ftp://www.website.com/uploads/ dsi.csv

chmod +w,+x,+r /Users/jwenzel/Library/Mail/POP-john.wenzeldata.com@integraonline.com/INBOX.mbox/Your_Daily_DSI_Prici.mimeattach/filename.csv 
rm /Users/jwenzel/Library/Mail/POP-john.wenzeldata.com@integraonline.com/INBOX.mbox/Your_Daily_DSI_Prici.mimeattach/W022601.csv

chmod +w,+x,+r /Users/jwenzel/Library/Mail/POP-john.wenzeldata.com@integraonline.com/INBOX.mbox/Your_Daily_DSI_Prici.mimeattach
rmdir /Users/jwenzel/Library/Mail/POP-john.wenzeldata.com@integraonline.com/INBOX.mbox/Your_Daily_DSI_Prici.mimeattach

