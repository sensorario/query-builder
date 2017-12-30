<?php

namespace Sensorario\QueryBuilder\Objects;

use Doctrine\ORM\QueryBuilder;

/**
 * @since Class available since Release 1.0.1
 */
class MetaData
{
    private $manager;

    private $entities;

    private $criteria;

    private $queryBuilder;

    public function __construct(
        \Doctrine\ORM\EntityManager $manager,
        Criteria $criteria
    ) {
        $this->criteria = $criteria;

        $this->manager = $manager;
    }

    public static function fromEntityManager(
        \Doctrine\ORM\EntityManager $manager,
        Criteria $criteria
    ) : MetaData {
        return new self($manager, $criteria);
    }

    public function getAllEntities() : array
    {
        return $this->manager
            ->getMetadataFactory()
            ->getAllMetadata();
    }

    public function getTable() : string
    {
        $className = $this->criteria->getClassName();

        $classMetaData = $this->manager->getClassMetadata($className);

        return $classMetaData->table['name'];
    }

    public function getCriteria() : Criteria
    {
        return $this->criteria;
    }

    public function getQueryBuilder() : QueryBuilder
    {
        return $this->manager->createQueryBuilder();
    }
}
