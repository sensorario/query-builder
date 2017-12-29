<?php

namespace Sensorario\QueryBuilder;

use Doctrine\ORM\QueryBuilder;

/**
 * @since Class available since Release 1.0.0
 */
final class Joiner
{
    private $metadata;

    private $selectBuilder;

    private $builder;

    public function init(
        SelectBuilder $selectBuilder,
        QueryBuilder  $builder,
        Objects\MetaData $metadata
    ) {
        $this->metadata      = $metadata;
        $this->builder       = $builder;
        $this->selectBuilder = $selectBuilder;

        $this->join();
    }

    private function join()
    {
        $defined = [$this->metadata->getTable()];
        foreach ($this->selectBuilder->willBeJoin() as $join) {
            foreach ($this->metadata->getAllEntities() as $entity) {
                if (in_array($entity->table['name'], [$join['from'], $join['to']])) {
                    if (!in_array($entity->table['name'], $defined)) {
                        $defined[] = $entity->table['name'];
                        $this->builder->join(
                            $entity->name,
                            $entity->table['name'],
                            'with',
                            $join['to'] . '.id = ' . $join['from'] . '.' . $join['to']
                        );
                    }
                }
            }
        }
    }

    public function getBuilder()
    {
        return $this->builder;
    }
}
