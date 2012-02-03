#!/bin/bash

# This script is used within the anyterm console

script=$1
logbase=$(dirname $0)/../../../../app/logs/scripts/
console=$(dirname $0)/../../../../app/console
scriptid=`basename $script`

mkdir -p $logbase
logfile=$logbase""$scriptid".log"

# Forward BitBucket key so we can temporarily use bitbucket on remote server (if remote connection is made with SSH agent forwarding)
# ONLY THE RSA KEY!!! Which is for bitbucket. Other  key id_dsa should NOT be added (security)
# @todo maybe we should move this into the script in the scriptbuilder??
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

# process log and remove temp script and log
echo "Saving log and clearing temporary files"
`$console publish:processlog --uid=$scriptid --exitcode=$exitcode`

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

# Self destruct
rm $script

exit $exitcode