#!/bin/sh

function usage
{
	echo "Creates a mysql user and database in local mysql environment"
    echo "Usage: mysql [options]"
	echo "-h, --help                 This help"
	echo "-p, --password             MySQL password to use"
	echo "-u, --user                 MySQL username to use"
	echo "-d, --database             MySQL database to use. If not given the username will be used as dbname"
	echo ""
}


function validate
{
	if [ -z "$password" ] || [ -z "$user" ]
	then
		usage
		exit 1
	fi
	
	if [ -z "$database" ]
	then
		database=$user
	fi
}


function createmysql
{
	# get global settings
	scriptdir=$(dirname $0)
	scriptdir=`cd $scriptdir; pwd`
	. $scriptdir/settings

	dbexists=`mysql -e "SHOW DATABASES LIKE '$database'" -u $mysql_user --password=$mysql_pw`
	userexists=`mysql -e "SELECT * FROM mysql.user WHERE USER LIKE '$user'" -u $mysql_user --password=$mysql_pw`

	dbcreate=1
	usercreate=1

	# DB creation
	if [ ! -z "$dbexists" ]
	then
#		read -p "Database $database already exists, DROP and CREATE a fresh one? [y/n] " confirmation
#		if [ $confirmation == 'y' ]
#		then
#			mysql -e "DROP DATABASE $database" -u $mysql_user --password=$mysql_pw
#		else
			echo "Database exists skipping database creation, moving on to user creation"
			dbcreate=0
#		fi
	fi

	if [ $dbcreate == 1 ]
	then
		echo "Creating database $database"
		mysql -e "CREATE DATABASE $database" -u $mysql_user --password=$mysql_pw
	fi


	# User creation
	if [ ! -z "$userexists" ]
	then
#		read -p "User $user already exists, DELETE and INSERT a fresh one? [y/n] " confirmation
#		if [ $confirmation == 'y' ]
#		then
#			mysql -e "DROP USER '$user'@localhost" -u $mysql_user --password=$mysql_pw
#		else
			echo "User exists skipping user creation"
			usercreate=0
#		fi
	fi

	if [ $usercreate == 1 ]
	then
		echo "Creating user $user"
		mysql -e "CREATE USER '$user'@'localhost' IDENTIFIED BY '$password'" -u $mysql_user --password=$mysql_pw
		echo "Granting privileges on $database"
		mysql -e "GRANT ALL PRIVILEGES ON $database.* TO '$user'@localhost" -u $mysql_user --password=$mysql_pw
		mysql -e "FLUSH PRIVILEGES" -u $mysql_user --password=$mysql_pw
	fi
}

while [ "$1" != "" ]; do
    case $1 in
		-h | --help )	
							usage
							exit
							;;
		-u | --user )		shift
							user=$1
							;;
		-p | --password )	shift
							password=$1
							;;
		-d | --database )	shift
							database=$1
							;;
        * )					echo ""
							echo "$1 is not a valid option"
							usage
							exit 1
    esac
    shift
done

validate
createmysql