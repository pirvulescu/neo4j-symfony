Neo4j - Symfony Project
==================================

# Start project (Mac)

Docker is required to be installed on the machine

  * Clone repository `git clone https://github.com/pirvulescu/neo4j-symfony.git`
  * Open directory  `cd currency_docker_symfony`
  * Start docker containers: `docker-compose up -d`
  * Log in to the PHP7 image: `docker-compose exec php-fpm bash`
  * install the project: `composer install`
  
Rest API endpoint will be available at the URL http://localhost:8082

Neo4j Browser http://localhost:7474
  
API endpoints:
 * GET /api/users
 * GET /api/users/{username}  
 * GET /api/friends/{username}  
 * POST /api/users
    ```
    Payload
    {
    	"username": "testUsername",
    	"name": "My test username",
    	"description": "Somethig here"
    }
    ```
 * DELETE /api/users/{username} 
 * PATCH /api/connect/{username}/{friendUsername}