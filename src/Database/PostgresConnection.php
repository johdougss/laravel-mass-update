<?php

namespace Johdougss\Database;

use Johdougss\Database\Query\Grammars\PostgresGrammar as QueryGrammar;
use Illuminate\Database\PostgresConnection as BaseDatabase;

class PostgresConnection extends BaseDatabase
{
    protected function getDefaultQueryGrammar()
    {
        ($grammar = new QueryGrammar)->setConnection($this);

        return $this->withTablePrefix($grammar);
    }
}
