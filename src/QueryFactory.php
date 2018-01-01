<?php

namespace Sensorario\QueryBuilder;

use Doctrine\ORM\QueryBuilder;

/**
 * @since Class available since Release 1.0.9
 */
class QueryFactory
{
    private $extractor;

    private $metadata;

    private $queryBuilder;

    public function __construct(
        Extractor $extractor,
        Objects\MetaData $metadata
    ) {
        $this->extractor    = $extractor;
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

    /**
     * @codeCoverageIgnore
     * @since method available since release 1.0.12
     */
    public function getExtractor()
    {
        return $this->extractor;
    }
}
