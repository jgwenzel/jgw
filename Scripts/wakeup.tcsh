#!/bin/tcsh
##2/16/22 UPDATE- NOW johngwenzel@gmail.com
##This script may be 15-20 years old and was written for a mac.
##It may work in Linux. If so, change the shebang and ext to .sh
##End of UPDATE

## Speaking Alarm Clock - Uses say command on mac

set i=0;
while( $i < 10 )
say -v Bruce Time to get up. Make coffee and have a smoke. It's going to be a great day. You have some cool stuff to carve with grey foam today. If you get up now, you'll have time to wake up and your day will go better.
set i = `expr $i + 1`;
end
say -v Bruce I'll leave you alone now

