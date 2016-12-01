composer install
find app/cache -type d -exec chmod 777 {} +
find app/cache -type f -exec chmod 666 {} +
app/console doctrine:schema:create