Organist
========================

Create database and schema:
app/console doctrine:database:create
app/consele doctrine:schema:create

Load Fixtures:
app/console doctrine:fixtures:load --fixtures=src/Netvlies/Bundle/PublishBundle/DataFixtures/Test --fixtures=src/Netvlies/Bundle/PublishBundle/DataFixtures/ORM
