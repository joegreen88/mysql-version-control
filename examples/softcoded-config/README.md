Example: softcoded database configuration
=========================================

**Level:** Advanced

This sample configuration loads the database connection from a php file with softcoded values that come from
the server environment.

 - composer.json points to a custom config file
 - the custom config file loads database config from the `$_SERVER` variable.

In this example the buildtime and runtime connections are identical.

## The details

The variable `$_SERVER["APP_ENV"]` needs to be one of the values in the *environments* section of `db/db.php`.

The following `$_SERVER` variables also need to be defined:

 - DB_HOST
 - DB_USERNAME
 - DB_PASSWORD
 - DB_NAME
 - DB_PORT 

## Usage

Just run the smyver commands without any additional parameters, e.g.:

    $ vendor/bin/smyver.php status development
    $ vendor/bin/smyver.php up development

## Notes

To use this set up you must run the smyver commands for each database environment from the correct app environment.
In other words, you cannot update the production db from the development environment.
You may consider this an inconvenience or a security enhancement.