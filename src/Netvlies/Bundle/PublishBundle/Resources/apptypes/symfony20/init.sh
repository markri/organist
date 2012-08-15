#!/bin/sh
cd ${repositorypath}
git clone ${gitrepo}
cd ${repokey}
wget http://symfony.com/download?v=Symfony_Standard_2.0.10.tgz
tar -zxvf Symfony_Standard_2.0.10.tgz
echo ""
mv Symfony/* .
rm -f Symfony_Standard_2.0.10.tgz
rmdir Symfony
rm app/config/parameters.ini
rm app/bootstrap.php.cache
cp -R ${initfiles} .
git add -A
git commit -m "Initial commit"
git push origin master