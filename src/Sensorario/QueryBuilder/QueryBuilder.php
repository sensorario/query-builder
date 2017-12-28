<?php

namespace Sensorario\QueryBuilder;

use Doctrine\ORM\EntityManager;

final class QueryBuilder
{
    private $manager;

    private $className;

    private $queryBuilder;

    private $criteria;

    private $joiner;

    public function __construct(
        EntityManager $manager,
        SelectBuilder $selectBuilder,
        Joiner $joiner
    ) {
        $this->manager       = $manager;
        $this->selectBuilder = $selectBuilder;
        $this->joiner        = $joiner;
    }

    public function setCriteria(array $criteria)
    {
        $this->className = $criteria['class'];
        $this->criteria = $criteria;

        return $this;
    }

    private function build()
    {
        $meta = $this->manager->getClassMetadata($this->className);
        $table = $meta->table['name'];

        $this->selectBuilder->setTable($table);
        $this->selectBuilder->addFields($this->criteria['what']);
        $select = join(', ', $this->selectBuilder->getFields());

        $this->queryBuilder = $this->manager->createQueryBuilder();
        $this->queryBuilder = $this->queryBuilder->select($select);
        $this->queryBuilder = $this->queryBuilder->from($this->className, $table);

        $this->joiner->init(
            $this->manager,
            $this->selectBuilder,
            $this->queryBuilder,
            $table
        );

        $this->queryBuilder = $this->joiner->getBuilder();
    }

    public function getQuery()
    {
        $this->build();

        return $this->queryBuilder->getQuery();
    }

    public function getResult()
    {
        return $this->getQuery()->getResult();
    }
}
