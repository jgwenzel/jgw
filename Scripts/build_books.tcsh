#/bin/tcsh
##2/16/22 UPDATE- NOW johngwenzel@gmail.com
##This script may be 15-20 years old and was written for a mac.
##It may work in Linux. If so, change the shebang and ext to .sh
##End of UPDATE

#script written by John Wenzel
set loopcount = 1
set filename = 'gfound1.html'

while($loopcount < 46000)

if( "$loopcount" > "1") then
	set filename = 'gfound1.html?start='$loopcount'&max=1000&letter='
endif

cat $filename | sed -n '/<table/,/<\/table>/p' | grep "<td>" | sed "s/'/\\'/g" | sed "s/<tr><td>/\'/g" | sed "s/<td>/,\'/g;s/<\/td>/\'/g" | sed "s/<a [^>]*>//g;s/<\/a>//g" > temp 
paste -s temp > temp2
./sednewline.tcsh '<\/tr>' temp2 > temp3
cat temp3 >> library
echo "Parsed $filename"
set loopcount = `expr $loopcount + 1000`
end
echo "Finished!"

exit 0

