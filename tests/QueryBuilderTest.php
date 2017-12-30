<?php

namespace Sensorario\QueryBuilder\Tests\Objects;

use PHPUnit\Framework\TestCase;
use Sensorario\QueryBuilder\QueryBuilder;
use Sensorario\QueryBuilder\Objects\Criteria;

class QueryBuilderTest extends TestCase
{
    public function testExtractClassNameFromMetadata()
    {
        $criteria = Criteria::fromArray([
            'class' => 'Foo\\Bar',
            'what' => []
        ]);

        $this->metadata = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Objects\MetaData')
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadata->expects($this->once())
            ->method('getCriteria')
            ->willReturn($criteria);

        $this->selectBuilder = $this
            ->getMockBuilder('Sensorario\QueryBuilder\SelectBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->joiner = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Joiner')
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new QueryBuilder(
            $this->metadata,
            $this->selectBuilder,
            $this->joiner
        );

        $this->assertEquals(
            'Foo\\Bar',
            $builder->getClassName()
        );
    }

    public function testExtractFieldsFromCriteria()
    {
        $criteria = Criteria::fromArray([
            'class' => 'Foo\\Bar',
            'what' => [
                'foo',
                'bar',
            ]
        ]);

        $this->metadata = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Objects\MetaData')
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadata->expects($this->once())
            ->method('getCriteria')
            ->willReturn($criteria);

        $this->selectBuilder = $this
            ->getMockBuilder('Sensorario\QueryBuilder\SelectBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->joiner = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Joiner')
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new QueryBuilder(
            $this->metadata,
            $this->selectBuilder,
            $this->joiner
        );

        $this->assertEquals(
            ['foo', 'bar'],
            $builder->getFields()
        );
    }

    public function testExtractSelectFields()
    {
        $criteria = Criteria::fromArray([
            'class' => 'Foo\\Bar',
            'what' => [
                'foo',
                'bar',
            ]
        ]);

        //$this->queryBuilder = $this
            //->getMockBuilder('Doctrine\ORM\QueryBuilder')
            //->disableOriginalConstructor()
            //->setMethods(['getQuery'])
            //->getMock();

        $this->metadata = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Objects\MetaData')
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadata->expects($this->once())
            ->method('getCriteria')
            ->willReturn($criteria);
        //$this->metadata->expects($this->once())
            //->method('getQueryBuilder')
            //->willReturn($this->queryBuilder);
        $this->metadata->expects($this->once())
            ->method('getTable')
            ->willReturn('bar');

        $this->selectBuilder = $this
            ->getMockBuilder('Sensorario\QueryBuilder\SelectBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->selectBuilder->expects($this->once())
            ->method('setTable')
            ->with('bar');
        $this->selectBuilder->expects($this->once())
            ->method('addFields')
            ->with(['foo', 'bar']);
        $this->selectBuilder->expects($this->once())
            ->method('getFields')
            ->willReturn([
                'bar.foo bar_foo',
                'bar.bar bar_bar',
            ]);

        $this->joiner = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Joiner')
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new QueryBuilder(
            $this->metadata,
            $this->selectBuilder,
            $this->joiner
        );

        $this->assertEquals(
            'bar.foo bar_foo, bar.bar bar_bar',
            $builder->extractSelectFields()
        );
    }

    public function testQuery()
    {
        $criteria = Criteria::fromArray([
            'class' => 'Foo\\Bar',
            'what' => [
                'foo',
                'bar',
            ]
        ]);

        $this->queryBuilder = $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['getQuery', 'select', 'from'])
            ->getMock();
        $this->queryBuilder->expects($this->once())
            ->method('select')
            ->with('bar.foo bar_foo, bar.bar bar_bar')
            ->willReturn($this->queryBuilder);
        $this->queryBuilder->expects($this->once())
            ->method('from')
            ->with('Foo\\Bar', 'bar');

        $this->metadata = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Objects\MetaData')
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadata->expects($this->once())
            ->method('getCriteria')
            ->willReturn($criteria);
        $this->metadata->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($this->queryBuilder);
        /** @todo force just once call */
        $this->metadata->expects($this->exactly(2))
            ->method('getTable')
            ->willReturn('bar');

        $this->selectBuilder = $this
            ->getMockBuilder('Sensorario\QueryBuilder\SelectBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->selectBuilder->expects($this->once())
            ->method('setTable')
            ->with('bar');
        $this->selectBuilder->expects($this->once())
            ->method('addFields')
            ->with(['foo', 'bar']);
        $this->selectBuilder->expects($this->once())
            ->method('getFields')
            ->willReturn([
                'bar.foo bar_foo',
                'bar.bar bar_bar',
            ]);

        $this->query = $this
            ->getMockBuilder('Doctrine\ORM\Query')
            ->disableOriginalConstructor()
            ->getMock();

        $this->ultimateQueryBuilder = $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['getQuery'])
            ->getMock();
        $this->ultimateQueryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->query);

        $this->joiner = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Joiner')
            ->disableOriginalConstructor()
            ->getMock();
        $this->joiner->expects($this->once())
            ->method('init')
            ->with(
                $this->selectBuilder,
                $this->queryBuilder,
                $this->metadata
            );
        $this->joiner->expects($this->once())
            ->method('getBuilder')
            ->willReturn($this->ultimateQueryBuilder);

        $builder = new QueryBuilder(
            $this->metadata,
            $this->selectBuilder,
            $this->joiner
        );

        $builder->getQuery();
    }

    public function testResult()
    {
        $criteria = Criteria::fromArray([
            'class' => 'Foo\\Bar',
            'what' => [
                'foo',
                'bar',
            ]
        ]);

        $this->queryBuilder = $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['getQuery', 'select', 'from'])
            ->getMock();
        $this->queryBuilder->expects($this->once())
            ->method('select')
            ->with('bar.foo bar_foo, bar.bar bar_bar')
            ->willReturn($this->queryBuilder);
        $this->queryBuilder->expects($this->once())
            ->method('from')
            ->with('Foo\\Bar', 'bar');

        $this->metadata = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Objects\MetaData')
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadata->expects($this->once())
            ->method('getCriteria')
            ->willReturn($criteria);
        $this->metadata->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($this->queryBuilder);
        /** @todo force just once call */
        $this->metadata->expects($this->exactly(2))
            ->method('getTable')
            ->willReturn('bar');

        $this->selectBuilder = $this
            ->getMockBuilder('Sensorario\QueryBuilder\SelectBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->selectBuilder->expects($this->once())
            ->method('setTable')
            ->with('bar');
        $this->selectBuilder->expects($this->once())
            ->method('addFields')
            ->with(['foo', 'bar']);
        $this->selectBuilder->expects($this->once())
            ->method('getFields')
            ->willReturn([
                'bar.foo bar_foo',
                'bar.bar bar_bar',
            ]);

        $this->query = $this
            ->getMockBuilder('Doctrine\ORM\Query')
            ->disableOriginalConstructor()
            ->setMethods(['getResult'])
            ->getMock();
        $this->query->expects($this->once())
            ->method('getResult')
            ->willReturn([]);

        $this->ultimateQueryBuilder = $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['getQuery'])
            ->getMock();
        $this->ultimateQueryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->query);

        $this->joiner = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Joiner')
            ->disableOriginalConstructor()
            ->getMock();
        $this->joiner->expects($this->once())
            ->method('init')
            ->with(
                $this->selectBuilder,
                $this->queryBuilder,
                $this->metadata
            );
        $this->joiner->expects($this->once())
            ->method('getBuilder')
            ->willReturn($this->ultimateQueryBuilder);

        $builder = new QueryBuilder(
            $this->metadata,
            $this->selectBuilder,
            $this->joiner
        );

        $builder->getResult();
    }
}
