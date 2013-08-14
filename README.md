Organist
========================

Setup
-----

Best way to setup Organist is by using the packer repository and generate a virtual image for it, so you may want to skip this section.
If you want to use it without packer and puppet than these are the instructions for setting up:

 - Clone this repository
 - Use composer to install vendors


Configure
----------

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

Todo
----

 - OTAP to DTAP
 - show reference as well