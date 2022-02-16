#!/bin/tcsh
##2/16/22 UPDATE- NOW johngwenzel@gmail.com
##This script may be 15-20 years old and was written for a mac.
##It may work in Linux. If so, change the shebang and ext to .sh
##End of UPDATE

#Set Variables
set mysql_host = "<ip-address>"
set mysql_user = "<username>"
set mysql_pass = "<password>"
set bu_dir = "<backup_dir>"
set log = "<logfile>"

#now dump database
mysqldump --all-databases > $bu_dir/db_tmp.sql -h $mysql_host -u $mysql_user $mysql_pass

#now move old backups up the ladder
cd $bu_dir/archive/
mv db.sql.bak13 db.sql.bak14
mv db.sql.bak12 db.sql.bak13
mv db.sql.bak11 db.sql.bak12
mv db.sql.bak10 db.sql.bak11
mv db.sql.bak9 db.sql.bak10
mv db.sql.bak8 db.sql.bak9
mv db.sql.bak7 db.sql.bak8
mv db.sql.bak6 db.sql.bak7
mv db.sql.bak5 db.sql.bak6
mv db.sql.bak4 db.sql.bak5
mv db.sql.bak3 db.sql.bak4
mv db.sql.bak2 db.sql.bak3
mv db.sql.bak1 db.sql.bak2
mv $bu_dir/db_all.sql $bu_dir/archive/db.sql.bak1

cd $bu_dir/
mv db_tmp.sql db_all.sql

#now write to backup log
echo __________________________________ >> $log
date >> $log
who >> $log
echo MySQL BACKUP via mysqldump >> $log
echo HOST: $mysql_host >> $log
echo USER: $mysql_user >> $log
echo BACKUP DIR: $bu_dir >> $log
ls -l $bu_dir >> $log
echo ARCHIVE DIR: $bu_dir/archive >> $log
ls -l $bu_dir/archive >> $log

