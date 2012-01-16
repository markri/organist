#!/bin/sh

function usage
{
	echo "Replace strings in existing files"
    echo "Usage: replaceinfile [options]"
	echo "-h, --help           This help"
	echo "-s, --search         Search for this string. Regex is possible"
	echo "-r, --replace        Replace with this string. Use '' for empty"
	echo "-f, --file           File to process"
	echo ""
}

function validate
{
	if [ -z "$search" ] || [ -z "$file" ]
	then
		echo "--search and --file are required"
		usage
		exit 1
	fi
	
	if [ -z "$replace" ]
	then
		replace=''
	fi
}


function replace
{
	echo 's#$search#$replace# $file'
	`sed -i -e "s#$search#$replace#" $file`
}


while [ "$1" != "" ]; do
    case $1 in
		-h | --help )		usage
							exit
							;;
		-s | --search )		shift
							search=$1
							;;
		-r | --replace )	shift
							replace=$1
							;;
		-f | --file )		shift
							file=$1
							;;
        * )					echo ""
							echo "$1 is not a valid option"
							usage
							exit 1
    esac
    shift
done

validate
replace




