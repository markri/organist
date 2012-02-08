#!/bin/sh


function usage
{
	echo ""
    echo "Usage: symfony [options]"
	echo "-h, --help                 This help"
	echo "-n, --name                 Project name"
	echo "-m, --mypass               MySQL password to use for the new project"
	echo ""
}

function validate
{
	if [ -z "$project" ] || [ -z "$password" ]
	then
		usage
		exit 1
	fi
}


function createproject
{
	scriptdir=$(dirname $0)
	scriptdir=`cd $scriptdir; pwd`
	. $scriptdir/settings

	#@todo this should be fixed dir for jenkins??
	projectdir=/var/www/vhosts/publish/web/repos/$project

	if [ -d $projectdir ]
	then
		echo "Project already exists, cant create project with same name!"
		exit 1
	fi

	mkdir -p $projectdir

	if [ ! -d $projectdir ]
	then
		echo "Cant create directory $projectdir ask administrator for help"
		exit
	fi

	cd $projectdir
	wget $symfony_download
	tar -zxvf $symfony_file
	echo ""
	mv Symfony/* .
	rm -f $symfony_file
	rm -rf Symfony
	
	cat $projectdir/app/config/parameters.ini | sed -e "s/database_name     = symfony/database_name     = $project/" > $projectdir/app/config/parameters.ini
	cat $projectdir/app/config/parameters.ini | sed -e "s/database_user     = root/database_user     = $project/" > $projectdir/app/config/parameters.ini
	cat $projectdir/app/config/parameters.ini | sed -e "s/database_password =/database_password = $password/" > $projectdir/app/config/parameters.ini
	
	
	# Add deployment descriptors
	# @todo
	cp $scriptdir/templates/build-symfony.xml $projectdir/build.xml
	cp $scriptdir/templates/deploy-symfony.rb $projectdir/app/config/deploy.rb
	cp $scriptdir/templates/deploy-symfony $projectdir/app/config/deploy
	
	# @todo
	# Add .gitignore
	
	# Add parameters.ini.dev
	# Add parameters.ini.test
	# Add parameters.ini.acc
	# Add parameters.ini.prod
	
	
	# Create bitbucket repository
	repo=`curl -X GET -s -u $bitbucket_user:$bitbucket_pw https://api.bitbucket.org/1.0/repositories/$bitbucket_user/$project | grep 'Not Found'`
	if [ ! -z "$repo" ]
	then
		echo 'Creating $project repository in BitBucket'
		echo ''
		curl -X POST -s -u $bitbucket_user:$bitbucket_pw https://api.bitbucket.org/1.0/repositories/ -d name=$project -d scm=git -d is_private=true -d language=php
	else
		echo 'Repository already exists. This shouldnt happen! Please contact system administrator to fix duplicate names'
		exit
	fi
	
	
	# Import in Repository
	echo ''
	echo 'Importing new project into GIT'	
	cd $projectdir
	git init
	git add -A
	git commit -m "Initial commit"
	#@todo add remote upstream before pushing

	
	git push
}


while [ "$1" != "" ]; do
    case $1 in
		-h | --help )	
						usage
						exit
						;;
		-n | --name )	shift
						project=$1
						;;
		-m | --mypass )	shift
						password=$1
						;;
        * )				echo ""
						echo "$1 is not a valid option"
						usage
						exit 1
    esac
    shift
done

validate
createproject
