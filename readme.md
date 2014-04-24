# sqli v0.5

[![Latest Stable Version](https://poser.pugx.org/pragmarx/sqli/v/stable.png)](https://packagist.org/packages/pragmarx/sqli) [![License](https://poser.pugx.org/pragmarx/sqli/license.png)](https://packagist.org/packages/pragmarx/sqli)

A Laravel 4 Artisan SQL Interactive Interface, plus a handful of Artisan commands to execute SQL queries.

## sqli

It's like tinker for SQL, just run

    php artisan sqli

And execute whatever sql query you like in your sql:

    postgresql:laravel> select email from users;

And you should see it this way:

    +----+------------------------------+
    | id | email                        |
    +----+------------------------------+
    |  1 | arnold@schwarzenegger.com    |
    |  2 | danny@devito.com             |
    +----+------------------------------+
    Executed in 0.0602 seconds.

You can get a list of your tables by running:

    postgresql:laravel> tables count

`count` option is optional:

    +--------------+-----------------------------+-----------+
    | table_schema | table_name                  | row_count |
    +--------------+-----------------------------+-----------+
    | public       | firewall                    | 2         |
    | public       | migrations                  | 3         |
    | public       | sessions                    | 1         |
    | public       | users                       | 1         |
    | public       | actors                      | 3431326   |
    | public       | movies                      | 1764727   |
    +--------------+-----------------------------+-----------+

You can change your current database connection by:

    postgresql:laravel> database mysql
    mysql:staging>

You can list all commands by executing

    postgresql:laravel> help

    +----------+------------------------------------------------------------------------------+
    | command  | description                                                                  |
    +----------+------------------------------------------------------------------------------+
    | quit     | Exit interface.                                                              |
    | tables   | List all tables. Use "tables count" to list with row count.                  |
    | help     | Show this help.                                                              |
    | database | Change the current database connection. Usage: "database [connection name]". |
    +----------+------------------------------------------------------------------------------+

To exit, just type `CTRL-C`, `CTRL-D` or `quit`.

## Other Artisan Commands

You don't need to enter sqli to execute commands, you have access to the most common DML commands via direct Artisan commands:

    select
    insert
    update
    delete

A special DML command, to execute whatever else you may needd:

    sql

And a command for listing tables:

    tables

## Syntax

The syntax could not be simpler, just execute 

    php artisan select email, first_name, last_name from users

And you should get a result like:

    +----+------------------------------+----------------+----------------+
    | id | email                        | first_name     | last_name      |
    +----+------------------------------+----------------+----------------+
    |  1 | arnold@schwarzenegger.com    | Arnold         | Schwarzenegger |
    |  2 | danny@devito.com             | Danny          | DeVito         |
    +----+------------------------------+----------------+----------------+

Create a very small alias for Artisan:

    alias a='php artisan'

And it'll be as if you where in your sql interface:

    a select * from posts where post_id < 100

    a update posts set author_id = 1

    a delete from posts

    a sql call removeOldPosts()

## Command 'table'

The command

    php artisan tables --count

Will give you a list of your tables with an optional row count:

    +--------------+-----------------------------+-----------+
    | table_schema | table_name                  | row_count |
    +--------------+-----------------------------+-----------+
    | public       | firewall                    | 2         |
    | public       | migrations                  | 3         |
    | public       | sessions                    | 1         |
    | public       | users                       | 1         |
    | public       | actors                      | 3431326   |
    | public       | movies                      | 1764727   |
    +--------------+-----------------------------+-----------+

## Too many columns aren't good to look at?

Use the `less` command to help you with that:

    a select * from users | less -S

Should give you a scrollable view of your table:

    +----+------------------------------+-------------+-----------+--------------------------------------------+---------------------+---------------------+--------------------------------------------------------------+---------------------+----------------+----------------+----------------------------+----------------------------+--------------------------------------------------------------+-----------+-------------+-----------+-----------+-------------+------------+--------------+------------------+-------------------+-----------------+-------------------+-----------------+-----------------+
    | id | email                        | permissions | activated | activation_code                            | activated_at        | last_login          | persist_code                                                 | reset_password_code | first_name     | last_name      | created_at                 | updated_at                 | password                                                     | gender_id | middle_name | nick_name | birth_day | birth_month | birth_year | early_signup | imported_from_id | registration_time | registration_ip | registrated_by_id | activation_time | beta_invitation |
    +----+------------------------------+-------------+-----------+--------------------------------------------+---------------------+---------------------+--------------------------------------------------------------+---------------------+----------------+----------------+----------------------------+----------------------------+--------------------------------------------------------------+-----------+-------------+-----------+-----------+-------------+------------+--------------+------------------+-------------------+-----------------+-------------------+-----------------+-----------------+
    | 38 | arnold@schwarzenegger.com    |             | 1         | V38ScwjCORUvCpuhjkieR4KbnQSlVbhFHujmsyVvN8 | 2014-02-16 14:07:59 | 2014-03-27 18:59:56 | $2y$10$POQ18Kc5JXftOtJswQujBO0PAQ4cfqsSXLKckn9aZOM4VgaExRDHa |                     | Arnold         | Schwarzenegger | 2014-03-29 18:38:39.998522 | 2014-03-27 18:59:56        | $2y$10$5S3KaI6PPHnySECVRwRcferQdiJZP6QgX5adxK7z/WPlxP386HW0e |           |             |           | 31        | 10          |            |              |                  |                   |                 |                   |                 |                 |
    | 40 | clint@eastwood.com           |             |           |                                            |                     |                     |                                                              |                     | Clint          | Eastwood       | 2014-03-29 18:38:39.998522 | 2014-03-29 18:26:17.402382 |                                                              |           |             |           |           |             |            |              |                  |                   |                 |                   |                 |                 |
    | 41 | paul@newman.com              |             |           |                                            |                     |                     |                                                              |                     | Paul           | Newman         | 2014-03-29 18:38:39.998522 | 2014-03-29 18:32:22.489968 |                                                              |           |             |           |           |             |            |              |                  |                   |                 |                   |                 |                 |
    +----+------------------------------+-------------+-----------+--------------------------------------------+---------------------+---------------------+--------------------------------------------------------------+---------------------+----------------+----------------+----------------------------+----------------------------+--------------------------------------------------------------+-----------+-------------+-----------+-----------+-------------+------------+--------------+------------------+-------------------+-----------------+-------------------+-----------------+-----------------+

## Drawbacks

When passing arguments to scripts Linux based systems may remove quotes and misunderstand your parentheses in queries, you if you need to use them you'll have to double quote it:

    a insert "into users (email, first_name, last_name, created_at, updated_at) values ('clint@eastwood.com', 'Clint', 'Eastwood', 'NOW', 'NOW')"

or just the parts that have them:

    a insert into users "(email, first_name, last_name, created_at, updated_at)" values "('clint@eastwood.com', 'Clint', 'Eastwood', 'NOW', 'NOW')"

But you can also escape them with \

    a update users set created_at = \'NOW\'

## Installation

### Requirements

- Laravel 4.1+

### Installing

Require the Select package:

    composer require "pragmarx/sqli" "0.*"

Due to a bug on Composer, on Windows clients you might need to add a colon in the requirement command line:

    composer require "pragmarx/sqli":"0.*"

Add the service provider to your app/config/app.php:

    'PragmaRX\SqlI\Vendor\Laravel\ServiceProvider',

## Author

[Antonio Carlos Ribeiro](http://twitter.com/iantonioribeiro) 

## License

Select is licensed under the BSD 3-Clause License - see the `LICENSE` file for details

## Contributing

Pull requests and issues are more than welcome.
