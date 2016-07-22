mysql-version-control
=====================

Effective mysql database version control in one library. Quick and simple to use, but with a rich and flexible
API for those who want to customise the set up.

## Requirements
 - PHP >= 5.4
 - Composer
 - MySQL client

Install with composer:

    $ composer require smrtr/mysql-version-control:~1.0

# Versioning
Your database versions will be stored in `<project_root>/db/versions` by default.
The sql for each version is stored in a directory directly under this directory.
So the directories are named `db/versions/1`, `db/versions/2` and so on.
Each version must contain at least one of the following files:

 - `schema.sql` - always runs first, contains `CREATE TABLE IF NOT EXISTS` and `ALTER` statements and the like.
 - `data.sql` - contains `REPLACE INTO`, `INSERT`, `UPDATE` and `DELETE` statements and the like.
 - `testing.sql` - same as `data.sql` but with test data which doesn't need to exist outside of testing environments.
 - `runme.php` - a custom php hook for running php code with your version.

The files for each version are run in the order specified above.

# Configuration
The quickest way to get started is to set up your database configuration in a file at `<project_root>/db/db.ini`.

See *examples/db.ini* for an example of this file.

### Environments
Define a list of environments and testing environments under the tag `[environments]`.

List out all of the available environments with entries like so: `environments[] = "local-dev"`.

List the testing environments like so: `testing_environments[] = "local-dev"`.
This list is a subset of the environments list and comprises those environments which should receive test data.

### Connections
You must define two database connection configurations for each environment.
The two configurations are called `buildtime` and `runtime` and they are used for processing schema changes and data
changes respectively.

Each connection requires a `host`, `user`, `password` and `database`. You can optionally add a `port`.

# Command Line Interface
The command line tool is located at `vendor/bin/smyver.php`. 

*Remember it!* It stands for **S**mrtr **MY**sql **VER**sion control.

## status
Run `vendor/bin/smyver.php status <environment>` to get the current status of the database for that environment.

## up
Run `vendor/bin/smyver.php up <environment>` to install or update the database on the given environment.
This command looks at the available versions in the `db/versions` directory and applies new versions sequentially
from the current version.

If this is the first run on the given environment, then a table called `db_config` is created and used to store the
current database version.

You may optionally provide a second argument specifying the mysql client binary to use.
This argument is required if mysql is not on your $PATH.

#### `--no-schema`
Use this flag to skip the schema files. This can be useful if you use an ORM to build the database schema.

#### `--install-provisional-version`
Use this flag to install a provisional version. This allows you to test out your database version, which may currently
be in development, before you commit to it by giving it a version number. This command looks for your provisional
version in `<project_root>/db/versions/new` by default.

## teardown
Run `vendor/bin/smyver.php teardown <environment>` to tear down the tables on the given environment.

This command is useful for development & testing environments.

Use the `confirm` option to bypass the confirmation prompt, e.g.

    vendor/bin/smyver.php <environment> --confirm

## Global CLI options
These options can be used with all console commands.

#### `--config-adapter`
Specify a configuration adapter to use instead of the Ini adapter which is used by default.

If you are using one of the standard adapters shipped with this package you only need to enter the class name,
e.g. PhpFile.

If you are using your own custom adapter class then you must provide a fully qualified class name and your class
must implement `Smrtr\MysqlVersionControl\DbConfigAdapter\ConfigAdapterInterface`.

#### `--config-adapter-param`
You can specify one or more constructor parameters for the configuration adapter class with this option.

To specify multiple parameters simply use the option more than once, e.g.
`--config-adapter-param="One" --config-adapter-param="Two"`
would result in the configuration adapter being instantiated like so: `new $adapter("One", "Two")`.

#### `--provisional-version`
Use this option to provide a custom path to your provisional version. Your custom path is relative to the versions path.

#### `--versions-path`
Use this option, or `-p` for short, to provide a custom path to your versions.
This allows you to override the default versions path which is `<project_root>/db/versions`.
If the path provided is not an absolute path then it is assumed to be relative to the project root.