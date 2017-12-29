<?php

namespace Sensorario\QueryBuilder\Tests\Objects;

use PHPUnit\Framework\TestCase;
use Sensorario\QueryBuilder\Objects\Criteria;
use Sensorario\QueryBuilder\Objects\MetaData;

class MetaDataTest extends TestCase
{
    public function setUp()
    {
        $this->manager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([
                'createQueryBuilder',
                'getClassMetadata',
                'getMetadataFactory',
            ])
            ->getMock();

        $this->criteria = Criteria::fromArray([
            'class' => 'Foo\\Bar',
            'what' => [],
        ]);
    }

    public function testExposeCriteria()
    {
        $this->manager->expects($this->never())
            ->method('createQueryBuilder');

        $this->manager->expects($this->never())
            ->method('getClassMetadata');

        $this->meta = MetaData::fromEntityManager(
            $this->manager,
            $this->criteria
        );

        $this->assertSame(
            $this->criteria,
            $this->meta->getCriteria()
        );
    }

    public function testProvideDoctrinesQueryBuilder()
    {
        $this->manager->expects($this->never())
            ->method('getClassMetadata');

        $this->queryBuilder = $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->meta = MetaData::fromEntityManager(
            $this->manager,
            $this->criteria
        );

        $this->assertInstanceOf(
            \Doctrine\ORM\QueryBuilder::class,
            $this->meta->getQueryBuilder()
        );
    }

    public function testProvideClassMetadata()
    {
        $this->manager->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->criteria->getClassName())
            ->willReturn(new class () {
                public $table = [
                    'name' => 'foo',
                ];
            });

        $this->manager->expects($this->never())
            ->method('createQueryBuilder');

        $this->meta = MetaData::fromEntityManager(
            $this->manager,
            $this->criteria
        );

        $this->assertEquals(
            'foo',
            $this->meta->getTable()
        );
    }

    public function testProvideAllEntitiesMetadata()
    {
        $this->factory = $this
            ->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataFactory')
            ->disableOriginalConstructor()
            ->setMethods(['getAllMetadata'])
            ->getMock();
        $this->factory->expects($this->once())
            ->method('getAllMetadata')
            ->willReturn($anArray = [
                'foo' => 'bar',
            ]);

        $this->manager->expects($this->once())
            ->method('getMetadataFactory')
            ->willReturn($this->factory);

        $this->manager->expects($this->never())
            ->method('createQueryBuilder');

        $this->meta = MetaData::fromEntityManager(
            $this->manager,
            $this->criteria
        );

        $this->assertEquals(
            $anArray,
            $this->meta->getAllEntities()
        );
    }
}
