# How to Test Project

**Run via Docker:**

`Docker compose up -d`

**Create data schema**

`symfony console --env=test doctrine:database:create`

`symfony console --env=test doctrine:schema:create`

`symfony console --env=test hautelook:fixtures:load`

**Run tests**

`./bin/phpunit`

