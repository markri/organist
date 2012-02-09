#!/bin/sh

function usage
{
	echo ""
	echo "Creates a vhost file for apache and reloads apache config"
    echo "Usage: apache [options]"
	echo "-h, --help            This help"
	echo "-dn, --domain         Domain to configure, can be used multiple times for multiple domains"
	echo "-s, --serverroot      Base directory where log files/directories will be stored"
	echo "-d, --docroot         From where apache should serve files"
	echo ""
}


function validate
{
	if [ -z "$serverroot" ] || [ -z "$docroot" ]
	then
		usage
		exit 1
	fi

	len=${#domains[*]}
	if [ $len == 0 ]
	then
		echo "I need at least one domain"
		usage
		exit 1		
	fi
}


function createvhost
{
	# get global settings
	scriptdir=$(dirname $0)
	scriptdir=`cd $scriptdir; pwd`
	. $scriptdir/settings
	firstdomain=${domains[0]}
	
	vhost=$vhost_base/$firstdomain.conf
	createvhost=1

	if [ -f $vhost ]
	then
		mv -f $vhost $vhost.bak
		rm -f $vhost
	fi

	if [ $createvhost == 1 ]
	then
		echo "Creating $vhost"
		touch $vhost
		
		exitcode=$?
		if [ $exitcode != 0 ]
		then
			echo "Couldnt create vhost file. Aborting"
			exit
		fi
		
		echo "<VirtualHost *:80>" >> $vhost
		echo "	DocumentRoot $docroot" >> $vhost
		echo "	ServerName $firstdomain" >> $vhost
		
		
		# loop through domains from first position to add ServerAlias for each domain it must listen to
		len=${#domains[*]}
		i=1
		while [ $i -lt $len ]; do
			echo "	ServerAlias ${domains[$i]}" >> $vhost
			let i++
		done	
		
		echo "	ErrorLog $serverroot/logs/error.log" >> $vhost
		echo "	TransferLog $serverroot/logs/transfer.log" >> $vhost
		echo "	<Directory $docroot>" >> $vhost
		echo "		AllowOverride All" >> $vhost
		echo "	</Directory>" >> $vhost
		echo "</VirtualHost>" >> $vhost
	fi
		

	#apache reload
	echo "Checking current apache config (with new vhost file)"
	$apache_daemon -t
	exitcode=$?
	if [ $exitcode != 0 ]
	then
		echo "Whoops, I probably created a wrong vhost file, wich cant be reloaded into apache.  So I left apache running with its current config"
		echo "Contact hosting to manually fix this vhost and ask for reload."
		exit
	fi

	$apache_daemon gracefull
}



while [ "$1" != "" ]; do
    case $1 in
		-h | --help )	
							usage
							exit
							;;
		-dn | --domain )	shift
							count=${#domains[*]}
							#let count++
							domains[$count]=$1						
							;;
		-s | --serverroot )	shift
							serverroot=$1
							;;							
		-d | --docroot )	shift
							docroot=$1
							;;
        * )					echo ""
							echo "$1 is not a valid option"
							usage
							exit 1
    esac
    shift
done

validate
createvhost