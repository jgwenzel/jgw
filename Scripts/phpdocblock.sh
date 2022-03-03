#!/bin/bash
#
# Copyright 2019 John Wenzel
#
# make executable:
#	
#   chmod +x phpdocblock.sh
#
# This command will create a block of prototype functions in a class
# that you can put at the top of that class for reference.
#
#     phpdocblock.sh YourFile.class.php
#
# This will echo the info to the terminal. Then you can copy those file 
# contents into your php. Or just use it as a utility. Note, it creates
# a tmp file in the process that you may discard.
#
# If you've ever had a class with many functions, you know it helps to have
# an index with line numbers.
# 
# If functions are organized into blocks like this, the 5-star comment
# will be grabbed too.
#
# (infile)
# /***** HERE'S A 5-STAR COMMENT
# */
# private function myfunction( string $a): void {...
# (endinfile)
#
# (output)
# /*
# 242   **** HERE'S A 5-STAR COMMENT
# 249      function myFunction (
# 277      function anotherFunction (
# */
# (endoutput)
#
# It will print out with 4 asterisks so pasting it into your code 
# file won't get grabbed the next time you use the command. 
#
#
echo > tmp
lines="$(grep -ic '\(private function\|protected function\|public function\|[*]\{5\}\)' $1)"
lines=$((lines + 1))
c=0
while [ $c -lt "$lines" ]; do
	echo >> tmp
	let c=c+1
done
cat $1 >> tmp

echo "/** Methods in " $1 
grep -in "\(private function\|protected function\|public function\|[*]\{5\}\)" tmp | sed 's/public\|protected\|private\|function//g' | sed 's/[[:space:]+]/ /g' | sed 's/\///g' | sed 's/[*]\{5,\}/\*\*\*\*/g'
echo "*/"

#The following was the original, but I think the grep command used works best
#grep -n "\(private\|protected\|public\) function" $1 | cut -d : -f 1 > docblock.tmp

