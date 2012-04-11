All files should have root as owner and be masked 700
You should make deploy_bridge available for a specific user(s) through visudo with NOPASSWD option
Through this file you can execute any other file below. So you have your own container of commands that can be executed

apache              Creates a vhost and reloads apache with new settings after testing is ok
deploy_bridge       Deploy bridge script, main entry point for all other scripts
mysql               Creates mysql user and database with appropriate rights
settings.php        Several system environment settings needed for other files
symfony2acl         Will set symfony2 ACL permissions on logs and cache folder using setfacl
testsettings        Checks if settings in settings.php are correct




@todo maybe we should have settings collection with
httpd_reload command
vhost_create command
mysql_add_user command
mysql_add_db command
syfmony2_acl command

and then used within deployment descriptors with variable replacement??
So we can use a deploybridge if we want to or just use httpd_reload or include sudo before, etc, etc