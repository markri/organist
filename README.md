# Organist #

[![Build Status](https://travis-ci.org/organist/organist.png?branch=master)](https://travis-ci.org/organist/organist)

Organist is an open source tool which helps you manage and execute deployments. It's built on top of Symfony2 and Capistrano/Capifony. [More @ http://organist.github.io](http://organist.github.io)


## Setup ##

This is the bare Organist application including the Symfony 2.2. framework. Unless you want to change/add customizations
to Organist, you may want to [start here to read instructions howto build up the box](https://github.com/organist/packer).
Which will build a fully functional box (this is due to some system dependencies like Anyterm)

## Manual Setup ##

If you want to use it without packer and puppet than these are the rough instructions for setting up:

 - Clone this repository
 - Use composer to install vendors
 - Install the Anyterm service

You still need to install [anyterm](http://anyterm.org/). And have your packages right. You can review this in the
[puppetscripts](https://github.com/organist/puppet). An exact guide can't be given due to the many dependant configuration
parameters and system dependencies used in Organist.


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
        symfony20:
            label: Symfony 2.0
        symfony21:
            label: Symfony 2.1
        myapplicationtype:
            label: My Super CMS
            userdirs: [ 'img', 'cache', 'lucene', 'tmp' ]
            userfiles: [ 'cms/.htpasswd', 'cms/.htaccess' ]

```

## License ##
Organist is licensed under the MIT licence. View the LICENSE file


## Todo ##

 - Add flexible parameter settings (per target/application/environment)
 - Please let me know by creating an issue
