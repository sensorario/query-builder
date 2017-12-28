<?php

namespace Sensorario\QueryBuilder;

use Doctrine\ORM\EntityManager;

final class CustomQueryBuilder
{
    private $criteria;

    public function __construct(array $criteria)
    {
        $this->criteria = $criteria;
    }

    public function __invoke(EntityManager $manager)
    {
        return (new \App\Services\QueryBuilder(
            $manager,
            new \App\Services\SelectBuilder(),
            new \App\Services\Joiner()
        ))
        ->setCriteria($this->criteria)
        ->getQuery();
    }
}
