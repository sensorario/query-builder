<?php

namespace Sensorario\QueryBuilder;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

/**
 * @since Class available since Release 1.0.0
 */
class QueryBuilder
{
    private $extractor;

    private $className;

    private $queryBuilder;

    private $fields;

    private $joiner;

    public function __construct(
        Extractor $extractor,
        Objects\MetaData $metadata,
        SelectBuilder $selectBuilder,
        Joiner $joiner
    ) {
        $this->extractor     = $extractor;
        $this->metadata      = $metadata;
        $this->selectBuilder = $selectBuilder;
        $this->joiner        = $joiner;

        $criteria = $this->metadata->getCriteria();
        $this->className = $criteria->getClassName();
        $this->fields = $criteria->getFields();

        $this->extractor->setFields($this->fields);
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
        $select = $this->extractor->extractSelectFields();

        $this->queryBuilder = $this->metadata->getQueryBuilder();
        $this->queryBuilder->select($select);
        $this->queryBuilder->from(
            $this->className,
            $this->metadata->getTable()
        );
    }

    public function getResult() : array
    {
        return $this->getQuery()->getResult();
    }
}
