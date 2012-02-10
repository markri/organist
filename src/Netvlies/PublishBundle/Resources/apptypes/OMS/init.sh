#!/bin/sh
cd ${repositorypath}
git clone ${gitrepo}
cd ${repokey}
git remote add oms git@bitbucket.org:netvlies/nvs-oms.git
git pull oms master
cp -R ${initfiles} .
git add -A
git commit -m "Initial commit"
git push origin master