<?php

namespace Sensorario\QueryBuilder;

use Doctrine\ORM\QueryBuilder;

/**
 * @since Class available since Release 1.0.9
 */
class QueryFactory
{
    private $extractor;

    private $queryBuilder;

    private $metadata;

    public function __construct(
        Extractor $extractor,
        QueryBuilder $queryBuilder,
        Objects\MetaData $metadata
    ) {
        $this->extractor    = $extractor;
        $this->queryBuilder = $queryBuilder;
        $this->metadata     = $metadata;
    }

    /** @since Class available since Release 1.0.3 */
    public function initQueryBuilder() : void
    {
        $select = $this->extractor->extractSelectFields();

        $criteria = $this->metadata->getCriteria();

        $this->queryBuilder = $this->metadata->getQueryBuilder();
        $this->queryBuilder->select($select);
        $this->queryBuilder->from(
            $criteria->getClassName(),
            $this->metadata->getTable()
        );
    }

    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }
}
