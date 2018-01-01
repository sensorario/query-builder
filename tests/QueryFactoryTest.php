<?php

namespace Sensorario\QueryBuilder\Tests\Objects;

use PHPUnit\Framework\TestCase;
use Sensorario\QueryBuilder\QueryFactory;

class QueryFactoryTest extends TestCase
{
    public function testInitializeQueryBuilderThrowExtractor()
    {
        $criteria = \Sensorario\QueryBuilder\Objects\Criteria::fromArray([
            'class' => 'Foo\\Bar',
            'what' => $fields = [
                'foo',
                'fizz.buzz',
            ]
        ]);

        $this->extractor = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Extractor')
            ->disableOriginalConstructor()
            ->getMock();
        $this->extractor->expects($this->once())
            ->method('extractSelectFields')
            ->willReturn('bar.foo bar_foo, bar.bar bar_bar');

        $this->queryBuilder = $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['select', 'from'])
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
            ->method('getQueryBuilder')
            ->willReturn($this->queryBuilder);
        $this->metadata->expects($this->once())
            ->method('getCriteria')
            ->willReturn($criteria);
        $this->metadata->expects($this->once())
            ->method('getTable')
            ->willReturn('bar');

        $factory = new QueryFactory(
            $this->extractor,
            $this->metadata
        );

        $factory->initQueryBuilder();

        $actualQueryBuilder = $factory->getQueryBuilder();

        $this->assertSame(
            $this->queryBuilder,
            $actualQueryBuilder
        );
    }
}
