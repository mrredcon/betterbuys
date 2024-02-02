# betterbuys
A simple eCommerce website for UTSA CS 3773

## Installation
To get started, make sure you have [Docker and Docker Compose installed](https://docs.docker.com/get-docker/).  After setting up Docker, simply clone the directory, `cd` into it, then run `docker compose up -d`.  This will build the PHP image, set up containers for nginx, php, and mysql, and run a webserver on the local machine running on port 30000.

## Usage
Try visiting [http://localhost:30000](http://localhost:30000) in your web browser!

## Reset data
To delete the database data, run `docker volume rm betterbuys_mysqldata` (assuming the name of the repository root is "betterbuys").
