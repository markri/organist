#!/bin/sh
cd ${repositorypath}
git clone ${gitrepo}
cd ${repokey}
git remote add sf21 git://github.com/symfony/symfony-standard.git
git pull sf21 master
echo ""
rm app/config/parameters.yml
cp -R ${initfiles} .
git add -A
git commit -m "Initial commit"
git push origin master
