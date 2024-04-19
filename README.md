
# How to Test Project

**Run via Docker:**


    
    docker compose up -d
    php composer install
    


**Create data schema**


    symfony console --env=test doctrine:database:create
    symfony console --env=test doctrine:schema:create
    symfony console --env=test hautelook:fixtures:load


**Run tests**


    ./bin/phpunit





**How I Handle Tasks Missions**

For security and checking roles, I use API Platform Annotation and set "security" to check if the user has a related role to create or delete a user.

`new Post(security: "is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_COMPANY_ADMIN')") `

`new Delete(security: "is_granted('ROLE_SUPER_ADMIN')") `

To prevent the update field "roles" by the user who has "ROLE_USER", I wrote a Voter class and set  "securityPostValidation" with "previous_object".

`new Patch(  securityPostValidation: "is_granted('POST_UPDATE', previous_object)")`

**Filter Data by role access:**
I wrote an ORM extension to filter user based on their company.
Two interfaces "QueryCollectionExtensionInterface", and "QueryItemExtensionInterface" help me to add my where query to the original query.


**Writing Test**

Use the "browser" bundle to test requests.
Use Factories to mock all the data I need.
