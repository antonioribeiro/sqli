# Select v0.5

[![Latest Stable Version](https://poser.pugx.org/pragmarx/select/v/stable.png)](https://packagist.org/packages/pragmarx/select) [![License](https://poser.pugx.org/pragmarx/select/license.png)](https://packagist.org/packages/pragmarx/select) [![Build Status](https://travis-ci.org/antonioribeiro/select.png)](https://travis-ci.org/antonioribeiro/select)

## Laravel Artisan Select

A handful of Artisan commands to execute SQL queries.

### Commands

You'll have access yo the most common DML commands, via Artisan:

    select
    insert
    update
    delete

A special DML command, to execute whatever else you may needd:

    sql

And a command for listing tables:

    table

### Syntax

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

### Command 'table'

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

### Drawbacks

When passing arguments to scripts Linux based systems may remove quotes and misunderstand your parentheses in queries, you if you need to use them you'll have to double quote (any part of) it:

    a insert "into users (email, first_name, last_name, created_at, updated_at) values ('clint@eastwood.com', 'Clint', 'Eastwood', 'NOW', 'NOW')"

or escape them all

    a insert into users \(email, first_name, last_name, created_at, updated_at\) values \(\'paul@newman.com\', \'Paul\', \'Newman\', \'NOW\', \'NOW\'\)

### Installation

#### Requirements

- Laravel 4.1+

#### Installing

Require the Select package:

    composer require pragmarx/select ~0

Add the service provider to your app/config/app.php:

    'PragmaRX\Select\Vendor\Laravel\ServiceProvider',

### Author

[Antonio Carlos Ribeiro](http://twitter.com/iantonioribeiro) 

### License

Select is licensed under the BSD 3-Clause License - see the `LICENSE` file for details

### Contributing

Pull requests and issues are more than welcome.
