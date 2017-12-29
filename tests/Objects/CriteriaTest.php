<?php

namespace Sensorario\QueryBuilder\Tests\Objects;

use PHPUnit\Framework\TestCase;
use Sensorario\QueryBuilder\Objects\Criteria;
use Sensorario\QueryBuilder\Exceptions\MissingClassNameException;

class CriteriaTest extends TestCase
{
    /**
     * @expectedException Sensorario\QueryBuilder\Exceptions\MissingClassNameException
     */
    public function testContainsClassName()
    {
        $criteria = Criteria::fromArray([]);
    }

    /**
     * @expectedException Sensorario\QueryBuilder\Exceptions\MissingWhatParamException
     */
    public function testContainsAlsoWhatParam()
    {
        $criteria = Criteria::fromArray([
            'class' => 'Foo\\Bar',
        ]);
    }

    public function testWrapCriteriaArrayIntoObject()
    {
        $criteria = Criteria::fromArray([
            'class' => 'Foo\\Bar',
            'what' => [
                'fooo',
            ],
        ]);

        $this->assertEquals('Foo\\Bar', $criteria->getClassName());
        $this->assertEquals(['fooo'], $criteria->getFields());
    }
}
