#!/bin/sh

function usage
{
	echo ""
	echo "Set symfony2 ACL for logs and cache folder"
    echo "Usage: symfony2acl [options]"
	echo "-h, --help            This help"
	echo "-b, --basedir      	Base directory of Symfony2 application"
	echo "-u, --user            User to set permission for"
	echo "-a, --httpduser       httpd user, defaults to apache"
	echo ""
}


function validate
{
	if [ -z "$basedir" ] || [ -z "$user" ]
	then
		usage
		exit 1
	fi
	
	if [ -z "$httpduser" ]
	then
		httpduser="apache"
	fi
}


function setacl
{
	`cd $basedir && sudo setfacl -R -m u:$httpduser:rwx -m u:$user:rwx app/logs app/cache`
	`cd $basedir && sudo setfacl -dR -m u:$httpduser:rwx -m u:$user:rwx app/logs app/cache`
}



while [ "$1" != "" ]; do
    case $1 in
		-h | --help )	
							usage
							exit
							;;
		-b | --basedir )	shift
							basedir=$1
							;;
		-u | --user )	    shift
							user=$1
							;;
		-a | --httpduser )	shift
							httpduser=$1
							;;							
        * )					echo ""
							echo "$1 is not a valid option"
							usage
							exit 1
    esac
    shift
done

validate
setacl

