# We use a debian:jessie base image, because node is already installed through composer
FROM debian:jessie
EXPOSE 8080
CMD cd /var/www/html/organist && \
    npm_package_config_port=8080 \
    npm_package_config_dbhost=mysql \
    npm_package_config_dbname=organist \
    npm_package_config_dbuser=root \
    npm_package_config_dbpassword=organist \
    npm_package_config_table=CommandLog \
    npm_package_config_idField=id  \
    npm_package_config_commandField=command \
    npm_package_config_logField=log \
    bin/node node_modules/organist-term/server.js >> organist.log