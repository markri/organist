#!/bin/sh

# get global settings
scriptdir=$(dirname $0)
scriptdir=`cd $scriptdir; pwd`
. $scriptdir/settings

count=0

if [ ! -d $vhost_base ] 
then
	echo "Directory set in vhost_base $vhost_base doesnt exist!"
	((count=$count+1))
fi

result=`which $apache_daemon`
exitcode=$?
if [ $exitcode != 0 ]
then
	echo "Apache deamon not found"
	((count=$count+1))
fi

result=`mysql -e "SELECT 1" -u $mysql_user --password=$mysql_pw`
exitcode=$?
if [ $exitcode != 0 ]
then
	echo "Wrong MySQL credentials"
	((count=$count+1))
fi

result=`grep "^$apache_user:" /etc/passwd`
exitcode=$?
if [ $exitcode != 0 ]
then
	echo "Unknown user $apache_user. Please assign the apache ACL user. Usually named 'apache'"
	((count=$count+1))
fi


if [ $count == 0 ]
then
	echo "Settings OK!"
else
	echo "In total $count warning(s)"	
	echo "Please see messages and correct your settings"
fi