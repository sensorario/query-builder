<?php

namespace Sensorario\QueryBuilder\Tests\Objects;

use PHPUnit\Framework\TestCase;
use Sensorario\QueryBuilder\SelectBuilder;

class SelectBuilderTest extends TestCase
{
    public function testBuildEmptyArrayOfFieldsIfUndefined()
    {
        $builder = new SelectBuilder();
        $this->assertEquals([], $builder->getFields());
    }

    /** @expectedException \Sensorario\QueryBuilder\Exceptions\MissingTableException */
    public function testThrowExceptionWheneverFieldIsAddedWithoutTable()
    {
        $builder = new SelectBuilder();
        $builder->addField('foo');
    }

    /** @expectedException \Sensorario\QueryBuilder\Exceptions\MissingTableException */
    public function testMoreFields()
    {
        $builder = new SelectBuilder();
        $builder->addFields(['foo']);
    }

    public function testPrependTableAndAppendUniqueAliasWithAllFields()
    {
        $builder = new SelectBuilder();
        $builder->setTable('tab');
        $builder->addFields(['foo']);
        $this->assertEquals(
            ['tab.foo tab_foo'],
            $builder->getFields()
        );
    }

    public function testPrependTableAndAppendUniqueAliasWithAddedField()
    {
        $builder = new SelectBuilder();
        $builder->setTable('tab');
        $builder->addField('foo');
        $this->assertEquals(
            ['tab.foo tab_foo'],
            $builder->getFields()
        );
    }

    public function testAddTableAndAliasAlsoConsideringRelations()
    {
        $builder = new SelectBuilder();
        $builder->setTable('tab');
        $builder->addField('foo');
        $builder->addField('fizz.bazz');
        $this->assertEquals(
            [
                'tab.foo tab_foo',
                'fizz.bazz fizz_bazz',
            ],
            $builder->getFields()
        );
    }

    public function testProvideRelationEntities()
    {
        $builder = new SelectBuilder();
        $builder->setTable('tab');
        $builder->addField('fizz.bazz');
        $builder->getFields();

        $this->assertEquals(
            [
                [
                    'from' => 'tab',
                    'to' => 'fizz',
                ],
            ],
            $builder->willBeJoin()
        );
    }
}
