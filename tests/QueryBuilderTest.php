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

        $this->joiner = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Joiner')
            ->disableOriginalConstructor()
            ->getMock();

        $this->extractor = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Extractor')
            ->disableOriginalConstructor()
            ->getMock();
        $this->extractor->expects($this->once())
            ->method('getMetadata')
            ->willReturn($this->metadata);

        $this->queryFactory = $this
            ->getMockBuilder('Sensorario\QueryBuilder\QueryFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->queryFactory->expects($this->once())
            ->method('getExtractor')
            ->willReturn($this->extractor);

        $builder = new QueryBuilder(
            $this->joiner,
            $this->queryFactory
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

        $this->joiner = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Joiner')
            ->disableOriginalConstructor()
            ->getMock();

        $this->extractor = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Extractor')
            ->disableOriginalConstructor()
            ->getMock();
        $this->extractor->expects($this->once())
            ->method('getMetadata')
            ->willReturn($this->metadata);

        $this->queryFactory = $this
            ->getMockBuilder('Sensorario\QueryBuilder\QueryFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->queryFactory->expects($this->once())
            ->method('getExtractor')
            ->willReturn($this->extractor);

        $builder = new QueryBuilder(
            $this->joiner,
            $this->queryFactory
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
            'what' => $fields = [
                'foo',
                'fizz.buzz',
            ]
        ]);

        $this->metadata = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Objects\MetaData')
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadata->expects($this->once())
            ->method('getCriteria')
            ->willReturn($criteria);

        $this->joiner = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Joiner')
            ->disableOriginalConstructor()
            ->getMock();

        $this->extractor = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Extractor')
            ->disableOriginalConstructor()
            ->getMock();
        $this->extractor->expects($this->once())
            ->method('setFields')
            ->with($fields)
            ->willReturn('asdfads');
        $this->extractor->expects($this->once())
            ->method('getMetadata')
            ->willReturn($this->metadata);

        $this->queryFactory = $this
            ->getMockBuilder('Sensorario\QueryBuilder\QueryFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->queryFactory->expects($this->once())
            ->method('getExtractor')
            ->willReturn($this->extractor);

        $builder = new QueryBuilder(
            $this->joiner,
            $this->queryFactory
        );
    }

    public function testBuildValidDoctrineQueryInstance()
    {
        $criteria = Criteria::fromArray([
            'class' => 'Foo\\Bar',
            'what' => $fields = [
                'foo',
                'fizz.buzz',
            ]
        ]);

        $this->queryBuilder = $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['getQuery', 'select', 'from'])
            ->getMock();

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

        $this->extractor = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Extractor')
            ->disableOriginalConstructor()
            ->getMock();
        $this->extractor->expects($this->once())
            ->method('setFields')
            ->with($fields);
        $this->extractor->expects($this->once())
            ->method('getMetadata')
            ->willReturn($this->metadata);
        $this->extractor->expects($this->once())
            ->method('getSelectBuilder')
            ->willReturn($this->selectBuilder);

        $this->queryFactory = $this
            ->getMockBuilder('Sensorario\QueryBuilder\QueryFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->queryFactory->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($this->queryBuilder);
        $this->queryFactory->expects($this->once())
            ->method('getExtractor')
            ->willReturn($this->extractor);

        $builder = new QueryBuilder(
            $this->joiner,
            $this->queryFactory
        );

        $builder->getQuery();
    }

    public function testObtainResultsFromModifiedQuery()
    {
        $criteria = Criteria::fromArray([
            'class' => 'Foo\\Bar',
            'what' => $fields = [
                'foo',
                'fizz.buzz',
            ]
        ]);

        $this->queryBuilder = $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['getQuery', 'select', 'from'])
            ->getMock();

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

        $this->query = $this
            ->getMockBuilder('Doctrine\ORM\Query')
            ->disableOriginalConstructor()
            ->setMethods(['getResult'])
            ->getMock();
        $this->query->expects($this->once())
            ->method('getResult')
            ->willReturn($expectedResults = [
                're' => 'sult',
            ]);

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

        $this->extractor = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Extractor')
            ->disableOriginalConstructor()
            ->getMock();
        $this->extractor->expects($this->once())
            ->method('setFields')
            ->with($fields);
        $this->extractor->expects($this->once())
            ->method('getMetadata')
            ->willReturn($this->metadata);
        $this->extractor->expects($this->once())
            ->method('getSelectBuilder')
            ->willReturn($this->selectBuilder);

        $this->queryFactory = $this
            ->getMockBuilder('Sensorario\QueryBuilder\QueryFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->queryFactory->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($this->queryBuilder);
        $this->queryFactory->expects($this->once())
            ->method('getExtractor')
            ->willReturn($this->extractor);

        $builder = new QueryBuilder(
            $this->joiner,
            $this->queryFactory
        );

        $actualResults = $builder->getResult();

        $this->assertEquals(
            $expectedResults,
            $actualResults
        );
    }
}
