<?php

namespace Sensorario\QueryBuilder;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

final class Joiner
{
    private $manager;

    private $selectBuilder;

    private $builder;

    private $table;

    public function init(
        EntityManager $manager,
        SelectBuilder $selectBuilder,
        QueryBuilder  $builder,
        string $table
    ) {
        $this->manager       = $manager;
        $this->builder       = $builder;
        $this->selectBuilder = $selectBuilder;
        $this->table         = $table;
    }

    private function join()
    {
        $allMeta = $this->manager->getMetadataFactory()
            ->getAllMetadata();

        $defined = [$this->table];
        foreach ($this->selectBuilder->willBeJoin() as $join) {
            foreach ($allMeta as $entity) {
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
        $this->join();

        return $this->builder;
    }
}
