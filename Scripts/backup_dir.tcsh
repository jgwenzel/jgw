#!/bin/tcsh

##2/16/22 UPDATE- NOW johngwenzel@gmail.com
##This script may be 15-20 years old and was written for a mac.
##It may work in Linux. If so, change the shebang and ext to .sh
##End of UPDATE

##This is an experimental backup script added by John Wenzel
##but was taken from 
##http://www.lostboi.com/tutorials/syncbackups.html
##2/16/22 Update - now johngwenzel@gmail.com

# Set the directories to be synchronised
set sync_dirs = ( Documents Library )

## Setup the variables
set main_dir = "/Users/jwenzel"
set backup_dir = "/Volumes/FW"
set log = "/backup/backup_dir_log"

# Start log entry
  echo _______________________________ >> $log
  echo DIRECTORY BACKUP >> $log
  date >> $log
  who >> $log
  echo _______BACKUP DIRECTORIES_______ >> $log


# Synch the directories one at a time
foreach mydir ($sync_dirs)
  rsync -r --quiet $main_dir/$mydir $backup_dir/$mydir
  ls -l $backup_dir/$mydir >> $log
  echo _____________ >> $log
end

#Also backup mysqldumps found in directory /backup
rsync -r --quiet /backup $backup_dir 
ls -l $backup_dir >> $log
echo _____________ >> $log

