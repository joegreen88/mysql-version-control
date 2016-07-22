Example: multiple database configurations
=========================================

**Level:** Advanced

In this sample configuration there are two separate databases, web and employees, being used by the application.

 - each database has its own versions path configured in composer.json
 - the custom config file loads database config from the `$_SERVER` variable.

In this example the buildtime and runtime connections are identical.

## The details

The variable `$_SERVER["APP_ENV"]` needs to be one of *development*, *qa*, *staging* and *production* as you can
see from looking at the *environments* section of `db/config.php`.

The following `$_SERVER` variables also need to be defined:

 - DB_WEB_HOST
 - DB_WEB_USERNAME
 - DB_WEB_PASSWORD
 - DB_WEB_NAME
 - DB_WEB_PORT
 - DB_EMPLOYEES_HOST
 - DB_EMPLOYEES_USERNAME
 - DB_EMPLOYEES_PASSWORD
 - DB_EMPLOYEES_NAME
 - DB_EMPLOYEES_PORT 

## Usage

Just run the smyver commands referencing the database you wish to operate on, e.g.:

    $ vendor/bin/smyver.php status web-development
    $ vendor/bin/smyver.php status employees-development
    $ vendor/bin/smyver.php up web-development
    $ vendor/bin/smyver.php up employees-development

## Notes

To use this set up you must run the smyver commands for each database environment from the correct app environment.
In other words, you cannot update the production db from the development environment.
You may consider this an inconvenience or a security enhancement.