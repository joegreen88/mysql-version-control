Example: multiple database configurations
=========================================

In this sample configuration there are two separate databases being used by the application.

Each database has its own versions path configured in composer.json.

## Usage
Just run the smyver commands referencing the database you wish to operate on, e.g.:

    $ vendor/bin/smyver.php status web-development
    $ vendor/bin/smyver.php status employees-development
    $ vendor/bin/smyver.php up web-development
    $ vendor/bin/smyver.php up employees-development
