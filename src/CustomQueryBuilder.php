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
            new \Sensorario\QueryBuilder\Joiner(),
            new \Sensorario\QueryBuilder\QueryFactory(
                new \Sensorario\QueryBuilder\Extractor(
                    new \Sensorario\QueryBuilder\SelectBuilder(),
                    $metadata
                ),
                $metadata
            )
        ))
        ->getQuery();
    }
}
