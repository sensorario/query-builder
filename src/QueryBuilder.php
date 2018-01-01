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

    private $joiner;

    private $factory;

    private $className;

    private $queryBuilder;

    private $fields;

    public function __construct(
        Extractor $extractor,
        Joiner $joiner,
        QueryFactory $factory
    ) {
        $this->extractor     = $extractor;
        $this->metadata      = $this->extractor->getMetadata();
        $this->joiner        = $joiner;
        $this->factory       = $factory;

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
        $this->joiner->init(
            $this->extractor->getSelectBuilder(),
            $this->factory->getQueryBuilder(),
            $this->metadata
        );

        $this->queryBuilder = $this->joiner->getBuilder();

        return $this->queryBuilder->getQuery();
    }

    public function getResult() : array
    {
        return $this->getQuery()->getResult();
    }
}
