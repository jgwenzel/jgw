#!/bin/tcsh
##2/16/22 UPDATE- NOW johngwenzel@gmail.com
##This script may be 15-20 years old and was written for a mac.
##It may work in Linux. If so, change the shebang and ext to .sh
##End of UPDATE

##This shell command is for Mac Terminal
##	./removenewlines replace_str filename > newfilename
##script written by The Flying Dutchman
##B is for Blog Administrator www.bisforblog.com

if ( "$2" == "") then 
	echo "Usage: $0 replace_str filename"
	exit 0
endif

set replace_str = $1
set filename = $2

cat $filename | sed 's/$/wow/g'

exit 0

