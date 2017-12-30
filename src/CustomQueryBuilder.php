<?php

namespace Sensorario\QueryBuilder;

/**
 * @since Class available since Release 1.0.0
 * @codeCoverageIgnore
 */
final class CustomQueryBuilder
{
    public function __invoke(Objects\MetaData $metadata)
    {
        return (new \Sensorario\QueryBuilder\QueryBuilder(
            $metadata,
            new \Sensorario\QueryBuilder\SelectBuilder(),
            new \Sensorario\QueryBuilder\Joiner()
        ))
        ->getQuery();
    }
}
