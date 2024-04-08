# betterbuys
A simple eCommerce website for UTSA CS 3773

## Installation
1. To get started, make sure you have [Docker and Docker Compose installed](https://docs.docker.com/get-docker/).
2. Clone the repository `git clone git@github.com:mrredcon/betterbuys.git` and `cd` into it.
3. Copy the example configuration file, `bb_config.ini.example`, to `bb_config.ini`.  This should live in the repository root next to docker-compose.yml and her friends.
4. Run `docker compose build`. This will build the PHP image, set up containers for nginx, php, and mysql.
5. Run `docker compose up -d`. This will run a webserver on the local machine running on port 30000.

# Making changes
Certain files are either cooked into the container's image during the "docker compose build" process, mounted from the host's filesystem directly as a volume, or as a secret.
If you edit `schema-queries.sql`, `bb_config.ini`, try running...
1. `docker compose build`
2. `docker compose up -d --force-recreate`

## Quirks
* Currently the root administrator user does not live in the database, do not try to reference userId=1 thinking that it exists.

## Usage
Try visiting [http://localhost:30000](http://localhost:30000) in your web browser!

## Reset data
To delete the database data, run `docker volume rm betterbuys_mysqldata` (assuming the name of the repository root is "betterbuys").
