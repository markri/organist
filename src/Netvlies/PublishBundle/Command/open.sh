# Please adjust script accordingly to your needs

while [ true ]
do
	workingdir=$1
	logfile='/var/www/logs/'`basename $1`'.txt'
	cd $workingdir
	read -p "bash$ " command
	# ssh command needs to be more secured than just ssh only app/console and bin/vendors should be allowed
	if echo $command | grep -Eq '^(ssh|ls|phing|app/console|bin/vendors) [^;]*$'
	then
		start=$(date +%s)
		echo "" >> $logfile
		echo `date`': '$command >> $logfile
		$command 2>&1 | tee -a $logfile
		exitcode=$PIPESTATUS
		end=$(date +%s)
		diff=$(( $end - $start ))
		
		if [ $exitcode != '0' ]
		then
			echo -e "\e[37m\e[41m Command $command returned with wrong exitcode. Please review settings and/or try again \e[0m"
		else
			if [ $diff != '0' ]
			then
				echo -e "\e[37m\e[42m Command $command was succesfully executed in $diff seconds \e[0m"
			else
				echo -e "\e[37m\e[42m Command $command was succesfully executed in almost zero seconds \e[0m"
			fi
		fi
	fi
done