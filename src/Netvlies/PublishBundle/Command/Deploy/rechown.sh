#!/bin/sh

function usage
{
	echo "Rechown specific files"
    echo "Usage: rechown [options]"
	echo "-h, --help              This help"
	echo "-d, --directory         Search within this directory"
	echo "-r, --replaceuser       Search for files owned by this user."
	echo "-u, --user              Use this username in chown"
	echo "-g, --group             Use this group in chown. If empty primary group will be used"
	echo ""
}

function validate
{
	if [ -z "$user" ] || [ -z "$directory" ]
	then
		echo "-u and -d are required"
		usage
		exit 1
	fi
	
	if [ -z "$group" ]
	then
		# get primary group from given user
		group=`id $user | sed -rn 's/.* gid=.*?\((.*)\) .*/\1/p'`
	fi
}


function rechown
{
	echo "find $directory -user deploy -exec chown $user:$group {} \;"
	find $directory -user $replaceuser -exec chown $user:$group {} \;
}


while [ "$1" != "" ]; do
    case $1 in
		-h | --help )		usage
							exit
							;;
		-r | --replaceuser )shift
							replaceuser=$1
							;;
		-u | --user )		shift
							user=$1
							;;
		-g | --group )		shift
							group=$1
							;;
		-d | --dir )		shift
							directory=$1
							;;
        * )					echo ""
							echo "$1 is not a valid option"
							usage
							exit 1
    esac
    shift
done

validate
rechown