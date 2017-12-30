<?php

namespace Sensorario\QueryBuilder;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

/**
 * @since Class available since Release 1.0.0
 */
class QueryBuilder
{
    private $className;

    private $queryBuilder;

    private $fields;

    private $joiner;

    public function __construct(
        /** SelectFieldsExtractor $extractor */
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

    /** @since Class available since Release 1.0.3 */
    public function getClassName()
    {
        return $this->className;
    }

    /** @since Class available since Release 1.0.3 */
    public function getFields()
    {
        return $this->fields;
    }

    public function getQuery() : Query
    {
        $this->initQueryBuilder();

        $this->joiner->init(
            $this->selectBuilder,
            $this->queryBuilder,
            $this->metadata
        );

        $this->queryBuilder = $this->joiner->getBuilder();

        return $this->queryBuilder->getQuery();
    }

    /** @since Class available since Release 1.0.3 */
    public function initQueryBuilder() : void
    {
        $select = $this->extractSelectFields();

        $this->queryBuilder = $this->metadata->getQueryBuilder();
        $this->queryBuilder->select($select);
        $this->queryBuilder->from(
            $this->className,
            $this->metadata->getTable()
        );
    }

    /** @since Class available since Release 1.0.3 */
    public function extractSelectFields() : string
    {
        $table = $this->metadata->getTable();
        $this->selectBuilder->setTable($table);
        $this->selectBuilder->addFields($this->getFields());
        return join(', ', $this->selectBuilder->getFields());
    }

    public function getResult() : array
    {
        return $this->getQuery()->getResult();
    }
}
