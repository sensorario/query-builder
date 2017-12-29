<?php

namespace Sensorario\QueryBuilder;

use Doctrine\ORM\EntityManager;

/**
 * @since Class available since Release 1.0.0
 */
final class QueryBuilder
{
    private $manager;

    private $className;

    private $queryBuilder;

    private $fields;

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

    public function setCriteria(Objects\Criteria $criteria)
    {
        $this->className = $criteria->getClassName();
        $this->fields = $criteria->getFields();

        return $this;
    }

    private function build()
    {
        $meta = $this->manager->getClassMetadata($this->className);
        $table = $meta->table['name'];

        $this->selectBuilder->setTable($table);
        $this->selectBuilder->addFields($this->fields);
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
