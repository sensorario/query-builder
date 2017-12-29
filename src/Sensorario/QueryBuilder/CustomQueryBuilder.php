<?php

namespace Sensorario\QueryBuilder;

use Doctrine\ORM\EntityManager;

/**
 * @since Class available since Release 1.0.0
 */
final class CustomQueryBuilder
{
    private $criteria;

    public function __construct(Objects\Criteria $criteria)
    {
        $this->criteria = $criteria;
    }

    public function __invoke(EntityManager $manager)
    {
        return (new \Sensorario\QueryBuilder\QueryBuilder(
            $manager,
            new \Sensorario\QueryBuilder\SelectBuilder(),
            new \Sensorario\QueryBuilder\Joiner()
        ))
        ->setCriteria($this->criteria)
        ->getQuery();
    }
}
