<?php

namespace Sensorario\QueryBuilder;

/**
 * @since Class available since Release 1.0.0
 */
final class SelectBuilder
{
    private $table;

    private $field = [];

    private $willBeJoin = [];

    public function setTable(string $table)
    {
        $this->table = $table;
    }

    public function addField(string $field) : void
    {
        $this->field[] = $field;
    }

    public function addFields(array $fields) : void
    {
        $this->field = array_merge(
            $this->field,
            $fields
        );
    }

    public function getFields() : array
    {
        $fields = [];

        foreach ($this->field as $field) {
            if (strpos($field, '.')) {
                // join ...
                $this->parseFieldForJoins($field);

                // select ...
                $exploded = explode('.', $field);
                $sliced = array_slice($exploded, -2, 2);
                $joined = join('.', $sliced);
                $snakeCaseJoin = join('_', $sliced);
                $join = $joined;
                $alias = $snakeCaseJoin;
            } else {
                $join = $this->table . '.' . $field;
                $alias = $this->table . '_' . $field;
            }

            $fields[] = $join . ' ' . $alias;
        }

        return $fields;
    }

    public function willBeJoin() : array
    {
        return $this->willBeJoin;
    }

    private function parseFieldForJoins($field) : void
    {
        $exploded = explode('.', $field);

        $from = count($exploded) == 2
            ? $this->table
            : array_slice($exploded, -3, 3)[0];

        $to = array_slice($exploded, -2, 2)[0];

        $this->willBeJoin[] = [
            'from' => $from,
            'to'   => $to,
        ];
    }
}

