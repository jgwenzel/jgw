#!/bin/tcsh
##2/16/22 UPDATE- NOW johngwenzel@gmail.com
##This script may be 15-20 years old and was written for a mac.
##It may work in Linux. If so, change the shebang and ext to .sh
##End of UPDATE

##This shell command is for Mac Terminal
##This command using sed to allow you to replace all matched strings in given file with a newline
##The string variable to be replaced [replace_str] can be regex and must be escaped as such
##Output can be written to a file using this example
##	./addnewlines replace_str filename > newfilename
##script written by The Flying Dutchman
##B is for Blog Administrator www.bisforblog.com

if ( "$2" == "") then 
	echo "Usage: $0 replace_str filename"
	exit 0
endif

set replace_str = $1
set filename = $2

cat $filename | sed 's/'$replace_str'/\\
/g'

exit 0

