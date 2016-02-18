# Organist #

Organist is an open source tool which helps you manage and execute deployments. It's built on top of Symfony2 and Capistrano/Capifony. [More @ http://organist.github.io](http://organist.github.io). I built it for Netvlies (netvlies.nl) to deploy application through through a DTAP stack. Main goal is to centralize security by not having passwords in git, but only in Organist and inject them during deployment.


## Setup ##

Currently Organist is distributed within a Symfony 2.3 stack. Setup is done by
     
     git clone https://github.com/markri/organist.git
     composer install
     
Organist works out of the box with Capistrano 2/3. You will need RVM to install both. Set up rvm @ https://rvm.io, install
ruby 1.8.7 if you want to use Capistrano 2. Install current ruby for Capistrano 3.
 
For Capistrano 2:

    rvm install 1.8.7
    rvm use 1.8.7
    gem install bundler
    cd setup/Capistrano2
    bundle install
    
For Capistrano 3 (Ruby version might be higher):
    
    rvm install 2.2.4
    rvm use 2.2.4
    gem install bundler
    cd setup/Capistrano3
    bundle install    


Once you're done setting up your environment for deploying, you'll need to start the terminal daemon. This is started with
following command (please change variables according to your setup):

    npm_package_config_port=8080 \
    npm_package_config_dbhost=localhost \
    npm_package_config_dbname=organist \
    npm_package_config_dbuser=root \
    npm_package_config_dbpassword=vagrant \
    npm_package_config_table=CommandLog \
    npm_package_config_idField=id  \
    npm_package_config_commandField=command \
    npm_package_config_logField=log \
    bin/forever start node_modules/organist-term/server.js



## Configure ##

Configure the path where repositories are stored for your deployment descriptors.

```yml
repository_path:  /home/deploy/organist/web/repos
```

In your config.yml

```yml
netvlies_publish:
    repositorypath: %repository_path%
    anyterm_user: deploy   # this local user is used for connecting to remote hosts for deployment and git user
    anyterm_exec_port: 7778 # anyterm port
    versioningservices:
        git:
            forward_key: /home/deploy/.ssh/id_rsa_bitbucket
    applicationtypes:
        symfony23:
            label: Symfony 2.3
        symfony25:
            label: Symfony 2.5
        myapplicationtype:
            label: My Super CMS
            userdirs: [ 'img', 'cache', 'lucene', 'tmp' ]
            userfiles: [ 'cms/.htpasswd', 'cms/.htaccess' ]

```

## License ##
Organist is licensed under the MIT licence. View the LICENSE file


## Upgrade from older versions ##

Update to correct scmService in Application entity
Update to correct application type in Application entity
Update to correct deployment strategy in Application entity

        UPDATE Application SET scmService = CONCAT('netvlies_publish.versioning', scmService);
        UPDATE Application SET applicationType = CONCAT('netvlies_publish.type.', applicationType);
        UPDATE Application SET deployment_strategy = CONCAT('netvlies_publish.strategy.capistrano2');


## Todo ##

 - Add flexible parameter settings (per target/application/environment)
 - Please let me know by creating an issue
