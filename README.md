# CloudCare Laravel Test

#### Author: Giuseppe Alessandro De Blasio

## Tech Stack

* PHP 8.1
* MySQL/MariaDB
* [Laravel 10](https://github.com/laravel/laravel)

# Package Dependencies

* [laravel/sail](https://laravel.com/docs/10.x/sail)
* [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth)

## System Requirements

* [Docker](https://www.docker.com)

## Installation

* Copy `.env.sail` to `.env`
* Check `APP_PORT` to make sure it does not conflict with some local webserver instance on same port (`80`)
* Check `FORWARD_DB_PORT` to make sure it does not conflict with some local MySQL instance on same port (`3306`)
* Check `PUNK_API_ENDPOINT` in case it's now different from default (https://api.punkapi.com/v2)
* Install Laravel Sail with the following command (no need to have PHP 8.1 installed locally):

  ```shell
  docker run --rm \
      -u "$(id -u):$(id -g)" \
      -v "$(pwd):/var/www/html" \
      -w /var/www/html \
      laravelsail/php81-composer:latest \
      composer install --ignore-platform-reqs
  ```
* Bring containers up with:
  ```shell
  ./vendor/bin/sail up -d
  ```
* Generate JWT secret with:
  ```shell
  ./vendor/bin/sail artisan jwt:secret
  ```
* Run database migrations and seeds with:
  ```shell
  ./vendor/bin/sail artisan migrate:fresh --seed
  ```

## Development

* Run static analysis with:
  ```shell
  ./vendor/bin/sail php ./vendor/bin/phpstan analyze
  ```
* Run code linting with:
  ```shell
  ./vendor/bin/sail php ./vendor/bin/duster lint
  ```
* Fix lint errors with:
  ```shell
  ./vendor/bin/sail php ./vendor/bin/duster fix
  ```

## Endpoints

* Login with `root` as a username and `password` as a password with (add `APP_PORT` to base URL if different from 80):
  ```shell
  curl --location 'http://localhost/api/auth/login' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
    "username": "root",
    "password": "password"
  }'
  ```

* Get `access_token` from response, e.g.:
  ```json
  {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwOC9hcGkvYXV0aC9sb2dpbiIsImlhdCI6MTY5NzY0NTYyNywiZXhwIjoxNjk3NjQ5MjI3LCJuYmYiOjE2OTc2NDU2MjcsImp0aSI6Im5pd1FWWHowSEIxc0Y2ZWwiLCJzdWIiOiIxIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.O4AbOZwRrpwoMTBV82yeK-hEE3XPyZQLzqUgBBcYVRg",
    "token_type": "bearer",
    "expires_in": 3600
  }
  ```

* After use is authenticated, fetch available beers from Punk API using JWT token from login response with (`page` defaults to 1 and `perPage` defaults to 20 if not specified):
  ```shell
  curl --location 'http://localhost/api/beers?page=1&perPage=5' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwOC9hcGkvYXV0aC9sb2dpbiIsImlhdCI6MTY5NzY0NTYyNywiZXhwIjoxNjk3NjQ5MjI3LCJuYmYiOjE2OTc2NDU2MjcsImp0aSI6Im5pd1FWWHowSEIxc0Y2ZWwiLCJzdWIiOiIxIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.O4AbOZwRrpwoMTBV82yeK-hEE3XPyZQLzqUgBBcYVRg'
  ```

## Bonus

* Logout user using JWT token from login response with:
  ```shell
  curl --location --request POST 'http://localhost/api/auth/logout' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwOC9hcGkvYXV0aC9sb2dpbiIsImlhdCI6MTY5NzY0NTYyNywiZXhwIjoxNjk3NjQ5MjI3LCJuYmYiOjE2OTc2NDU2MjcsImp0aSI6Im5pd1FWWHowSEIxc0Y2ZWwiLCJzdWIiOiIxIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.O4AbOZwRrpwoMTBV82yeK-hEE3XPyZQLzqUgBBcYVRg'
  ```

* Refresh JWT token using current token from login response with:
  ```shell
  curl --location --request POST 'http://localhost/api/auth/refresh' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwOC9hcGkvYXV0aC9sb2dpbiIsImlhdCI6MTY5NzY0NTYyNywiZXhwIjoxNjk3NjQ5MjI3LCJuYmYiOjE2OTc2NDU2MjcsImp0aSI6Im5pd1FWWHowSEIxc0Y2ZWwiLCJzdWIiOiIxIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.O4AbOZwRrpwoMTBV82yeK-hEE3XPyZQLzqUgBBcYVRg'
  ```

## Testing

* Run tests and show code coverage with:
  ```shell
  ./vendor/bin/sail artisan test --coverage
  ```
