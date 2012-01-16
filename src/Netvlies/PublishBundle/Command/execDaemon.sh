result=`ps afx | grep '\-p 7778' | grep -v grep | wc -l`
if [ $result == 1 ]
then
	echo "Daemon already running"
	exit
fi

cwd=$(dirname $(readlink -f $0))
`anytermd -c "$cwd/exec.sh %p" -p 7778 -u jenkins`
echo "Daemon started"