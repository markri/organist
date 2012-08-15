#!/bin/sh
cd ${repositorypath}
git clone ${gitrepo}
cd ${repokey}
cp -R ${initfiles} .
git add -A
git commit -m "Initial commit"
git push origin master