# Organist #

Organist is an open source tool which helps you manage and execute deployments. It's built on top of Symfony2 and Capistrano/Capifony. [More @ http://organist.github.io](http://organist.github.io). I built it for Netvlies (netvlies.nl) to deploy application through through a DTAP stack. Main goal is to centralize security by not having passwords in git, but only in Organist and inject them during deployment.


## Setup ##

Organist is executed in a Docker environment

    docker-compose up -d
    docker exec -ti organist_phpcli
    ./setup.sh

For Demo data you could use:

    app/console doctrine:fixtures:load -n

## Notes ##

Currently it is fixed to use BitBucket and to use git


## License ##
Organist is licensed under the MIT licence. View the LICENSE file
