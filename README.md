mysql-version-control
=====================

A crude version control system for a mysql database.

## Installation
Use composer.
Add `"smrtr/mysql-version-control": "~1.0"` to the `requires` section of your project's composer.json then run
`composer update`.

## Configuration
Your database configuration will be stored at `<project_root>/db/db.ini`. In this file you will define a list of
environments and then define database configurations for each environment.

### environments
Define a list of environments under the `[environments]` tag using the format `environments[] = "development"`. The
CLI tool will add commands for each environment listed here.

You may also define a list of testing environments using the format `testing_environments[] = "testing"`. The CLI tool
will only apply test data on the environments listed here.

### databases
You must define two database configurations for each environment using the name of the environment as a tag.
The two configurations are called `buildtime` and `runtime` and they are used for processing schemas and data
respectively. Each configuration requires a `host`, `user`, `password` and `database` entry.

### Example db.ini

    [environments]
    environments[] = "development"
    environments[] = "production"

    testing_environments[] = "development"

    [development]
    runtime.host = "localhost"
    runtime.user = "buzz"
    runtime.password = "lightyear"
    runtime.database = "buzz"

    buildtime.host = "localhost"
    buildtime.user = "buzz"
    buildtime.password = "lightyear"
    buildtime.database = "buzz"

    [production]
    runtime.host = "localhost"
    runtime.user = "root"
    runtime.password = "root"
    runtime.database = "buzz"

    buildtime.host = "localhost"
    buildtime.user = "buzz"
    buildtime.password = "lightyear"
    buildtime.database = "buzz"

## Versioning
Your database versions will be stored in `<project_root>/db/versions` by default.
The sql for each version is stored in a directory directly under this directory.
So the directories are named `db/versions/1`, `db/versions/2` and so on.
Each version must contain at least one of the following files:

 - `schema.sql` - always runs first, contains `CREATE TABLE IF NOT EXISTS` and `ALTER` statements.
 - `data.sql` - contains `REPLACE INTO`, `INSERT`, `UPDATE` and `DELETE` statements and the like.
 - `testing.sql` - same as `data.sql` but with test data which doesn't need to exist outside of testing environments.
 - `runme.php` - a custom php hook for running, for example, import tasks.

The files are run in the order specified above.

## Usage
This package will put two CLI scripts into your project's `vendor/bin` directory.

### up
Run `vendor/bin/up <environment>` to install or update the database on the given environment.
This command looks at the available versions in the `db/versions` directory and applies new versions sequentially
from the current version.

If this is the first run on the given environment, then a table called `db_config` is created and used to store the
current database version.

#### `--no-schema`
Use this flag to skip the schema files. This can be useful if you use an ORM to build the database schema.

#### `--versions-path`
Use this option, or `-p` for short, to provide a custom path to your versions.
This allows you to override the default versions path which is `<project_root>/db/versions`.

#### `--install-provisional-version`
Use this flag to install a provisional version. This allows you to test out your database version, which may currently
be in development, before you commit to it by giving it a version number. This command looks for your provisional
version in `<project_root>/db/versions/new` by default.

#### `--provisional-version`
Use this option to provide a custom path to your provisional version. Your custom path is relative to the versions path.

### teardown
Run `vendor/bin/teardown <environment>` to tear down the tables on the given environment.

This command is useful for development & testing developments where you may wish to, for example, tear down your
database between test runs.

Use the `confirm` option to bypass the confirmation prompt, e.g.

    vendor/bin/teardown <environment> --confirm
