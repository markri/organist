#!/bin/bash

# Please adjust script accordingly to your needs
script=$1
logbase=$(dirname $0)/../../../../app/logs/scripts/
console=$(dirname $0)/../../../../app/console
scriptid=`basename $script`

mkdir -p $logbase
logfile=$logbase""$scriptid".log"

# ONLY THE RSA KEY!!! Which is for bitbucket. Other  key id_dsa should NOT be added (security)
eval `ssh-agent`
`ssh-add $HOME/.ssh/id_rsa`

# keep track of execution time
start=$(date +%s)

# init logfile
echo "" >> $logfile
echo `date` >> $logfile
echo `cat -A $script` >> $logfile
echo "" >> $logfile

# execute script
$script | tee -a $logfile
exitcode=${PIPESTATUS[0]}

# calculate duration
end=$(date +%s)
diff=$(( $end - $start ))

ssh-agent -k > /dev/null 2>&1
unset SSH_AGENT_PID
unset SSH_AUTH_SOCK

# check if script was succesfull
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

# process log and remove script
`$console publish:processlog --uid=$scriptid --exitcode=$exitcode`
rm $script

# Somehow this is needed in order to return all output back to the terminal, why???
read -n1