<?php

namespace Johdougss\Database\Query\Grammars;

use Exception;
use Illuminate\Database\Query\Grammars\PostgresGrammar as BasePostgresGrammar;

class PostgresGrammar extends BasePostgresGrammar
{
    /**
     * @throws Exception
     */
    public function compileJoinFrom(array $items, $tableAlias)
    {
        if (count($items) === 0) {
            throw new Exception('items is empty');
        }

        $values = [];
        $properties = array_keys($items[0]);
        foreach ($items as $item) {
            $valuesItem = [];
            foreach ($properties as $property) {
                $valuesItem[] = $this->parameter($item[$property]);
            }

            $values[] = '(' . implode(', ', $valuesItem) . ')';
        }

        $values = implode(', ', $values);
        $properties = implode(', ', $properties);

        return "(values $values) as $tableAlias ($properties)";
    }

    public function prepareBindingsJoinFrom(array $items)
    {
        $values = [];

        $properties = array_keys($items[0]);
        foreach ($items as $item) {
            foreach ($properties as $property) {
                $values[] = $item[$property];
            }
        }

        return $values;
    }
}
