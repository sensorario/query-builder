<?php

namespace Sensorario\QueryBuilder;

/** @since Class available since Release 1.0.4 */
class Extractor
{
    private $metadata;

    private $selectBuilder;

    private $fields;

    public function __construct(
        SelectBuilder $selectBuilder,
        Objects\MetaData $metadata
    ) {
        $this->selectBuilder = $selectBuilder;
        $this->metadata      = $metadata;
    }

    public function extractSelectFields() : string
    {
        $this->ensureFieldsAreDefined();

        $table = $this->metadata->getTable();

        $this->selectBuilder->setTable($table);
        $this->selectBuilder->addFields($this->getFields());

        return join(', ', $this->selectBuilder->getFields());
    }

    public function setFields(array $fields) : void
    {
        $this->fields = $fields;
    }

    public function getFields() : array
    {
        return $this->fields;
    }

    public function ensureFieldsAreDefined() : void
    {
        if (!$this->fields) {
            throw new \RuntimeException(
                'Oops! '
            );
        }
    }

    /** @since Class available since Release 1.0.7 */
    public function getSelectBuilder() : SelectBuilder
    {
        return $this->selectBuilder;
    }

    /** @since Class available since Release 1.0.7 */
    public function getMetadata() : Objects\MetaData
    {
        return $this->metadata;
    }
}
