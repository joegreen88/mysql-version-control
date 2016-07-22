Example: softcoded database configuration
=========================================

This sample configuration loads the database connection from a php file with softcoded values that come from
the server environment.

In this example the buildtime and runtime connections are identical.

The following `$_SERVER` variables need to be defined:

 - APP_ENV
 - DB_HOST
 - DB_USERNAME
 - DB_PASSWORD
 - DB_NAME
 - DB_PORT
 
The variable `$_SERVER["APP_ENV"]` needs to one of the values in the *environments* section of `db/db.php`.

To use this set up you must run the smyver commands for each database environment from the correct app environment.
In other words, you cannot update the production db from the development environment.
You may consider this an inconvenience or a security enhancement.

## Usage
Just run the smyver commands without any additional parameters, e.g.:

    $ vendor/bin/smyver.php status development
    $ vendor/bin/smyver.php up development
