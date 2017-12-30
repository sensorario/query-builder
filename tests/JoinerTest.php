<?php

namespace Sensorario\QueryBuilder\Tests\Objects;

use PHPUnit\Framework\TestCase;
use Sensorario\QueryBuilder\Joiner;

class JoinerTest extends TestCase
{
    public function testProvideQueryBuilderPreviouslyInjected()
    {
        $this->selectBuilder = $this
            ->getMockBuilder('Sensorario\QueryBuilder\SelectBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBuilder = $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadata = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Objects\MetaData')
            ->disableOriginalConstructor()
            ->getMock();

        $joiner = new Joiner();

        $joiner->init(
            $this->selectBuilder,
            $this->queryBuilder,
            $this->metadata
        );

        $this->assertSame(
            $this->queryBuilder,
            $joiner->getBuilder()
        );
    }

    public function testNeverCallsJoinWhenEntitiesDoesNotContainRelations()
    {
        $this->selectBuilder = $this
            ->getMockBuilder('Sensorario\QueryBuilder\SelectBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->selectBuilder->expects($this->once())
            ->method('willBeJoin')
            ->willReturn([
                [
                    'from' => 'tab',
                    'to' => 'fizz',
                ]
            ]);

        $this->queryBuilder = $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods([
                'join',
            ])
            ->getMock();
        $this->queryBuilder->expects($this->never())
            ->method('join');

        $this->queryBuilder = $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadata = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Objects\MetaData')
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadata->expects($this->once())
            ->method('getAllEntities')
            ->willReturn([
                new class () {
                    public $table = [
                        'name' => 'non in join',
                    ];
                },
            ]);

        $joiner = new Joiner();

        $joiner->init(
            $this->selectBuilder,
            $this->queryBuilder,
            $this->metadata
        );

        $this->assertSame(
            $this->queryBuilder,
            $joiner->getBuilder()
        );
    }

    public function testCallsJoinWhenEntitiesContainRelations()
    {
        $this->selectBuilder = $this
            ->getMockBuilder('Sensorario\QueryBuilder\SelectBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->selectBuilder->expects($this->once())
            ->method('willBeJoin')
            ->willReturn([
                [
                    'from' => 'tab',
                    'to' => 'fizz',
                ]
            ]);

        $this->queryBuilder = $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods([
                'join',
            ])
            ->getMock();
        $this->queryBuilder->expects($this->once())
            ->method('join');

        $this->metadata = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Objects\MetaData')
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadata->expects($this->once())
            ->method('getAllEntities')
            ->willReturn([
                new class () {
                    public $name = 'an entity name';
                    public $table = [
                        'name' => 'tab',
                    ];
                },
            ]);

        $joiner = new Joiner();

        $joiner->init(
            $this->selectBuilder,
            $this->queryBuilder,
            $this->metadata
        );

        $this->assertSame(
            $this->queryBuilder,
            $joiner->getBuilder()
        );
    }
}
