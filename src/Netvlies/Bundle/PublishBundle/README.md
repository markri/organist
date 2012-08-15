Dependencies
- anyterm with apachy proxy setting to /console/exec and /console/open
- php53
- php-ssh2 package
- pear
- phing (for deployment of sudo files) (improve this OTAP-52)


Setup
- be sure to set permissions right of the app/cache and app/log directory
- A private key with public key deployed in authorized_keys foreach environment
- A private and public key in exec.sh for each GIT repository (github, bitbucket)
- config.yml settings
     repositorypath: /var/www/vhosts/publish/web/repos # where repositories are downloaded for the build files/ deployment descriptors
     sudouser: deploy # remote sudo user, e.g. reloading apache, mysql user creation, etc
     privkeyfile: /var/lib/jenkins/.ssh/id_dsa # private key to use
     pubkeyfile: /var/lib/jenkins/.ssh/id_dsa.pub # public key to use
- Running anyterm under another user (not apache) to protect private key. In our case 'jenkins' to use a shared user
- Deploy sudo management files


Packages that are handy
- sshfs to mount remote filesystem so you can rsync it with another host
- capistrano would be nice



Advantages
- Even your grandma can deploy a webapplication!
- Easy to use interface to manage multiple deployments
- See which version is deployed on which server with revision/version
- Centralized storage of credentials, no need of putting these in your repository
- Knowing who deployed something and when
- Error logging during deployment
- Flexible to extend/use it with your own type of applications and/or parameters
