#!/bin/bash

result=`ps afx | grep '\-p 7779' | grep -v grep | wc -l`
if [ $result == 1 ]
then
	echo "Daemon already running"
	exit
fi

cwd=$(dirname $(readlink -f $0))
`anytermd -c "$cwd/open.sh %p" -p 7779 -u apache`
echo "Daemon started"