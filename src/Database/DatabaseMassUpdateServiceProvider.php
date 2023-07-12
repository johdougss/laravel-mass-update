<?php

namespace Johdougss\Database;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;

class DatabaseMassUpdateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Connection::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
            return new PostgresConnection($connection, $database, $prefix, $config);
        });

        Builder::macro('joinFrom', function (array $items, $tableAlias, $first, $operator = null, $second = null, $type = 'inner', $where = false) {
            $table = $this->grammar->compileJoinFrom($items, $tableAlias);

            $bindings = $this->grammar->prepareBindingsJoinFrom($items);

            $this->addBinding($bindings, 'join');

            return $this->join($this->raw($table), $first, $operator, $second, $type, $where);
        });
    }
}
