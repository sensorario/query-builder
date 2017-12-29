<?php

namespace Sensorario\QueryBuilder;

use Doctrine\ORM\EntityManager;

/**
 * @since Class available since Release 1.0.0
 */
final class QueryBuilder
{
    private $className;

    private $queryBuilder;

    private $fields;

    private $joiner;

    public function __construct(
        Objects\MetaData $metadata,
        SelectBuilder $selectBuilder,
        Joiner $joiner
    ) {
        $this->metadata      = $metadata;
        $this->selectBuilder = $selectBuilder;
        $this->joiner        = $joiner;

        $criteria = $this->metadata->getCriteria();
        $this->className = $criteria->getClassName();
        $this->fields = $criteria->getFields();
    }

    private function build()
    {
        $this->selectBuilder->setTable($this->metadata->getTable());
        $this->selectBuilder->addFields($this->fields);
        $select = join(', ', $this->selectBuilder->getFields());

        $this->queryBuilder = $this->metadata->getQueryBuilder();

        $this->queryBuilder = $this->queryBuilder->select($select);

        $this->queryBuilder = $this->queryBuilder->from(
            $this->className,
            $this->metadata->getTable()
        );

        $this->joiner->init(
            $this->selectBuilder,
            $this->queryBuilder,
            $this->metadata
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
