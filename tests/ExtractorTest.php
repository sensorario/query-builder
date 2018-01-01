<?php

namespace Sensorario\QueryBuilder\Tests\Objects;

use PHPUnit\Framework\TestCase;
use Sensorario\QueryBuilder\Extractor;

class ExtractorTest extends TestCase
{
    /** @expectedException \Sensorario\QueryBuilder\Exceptions\MissingFieldsException */
    public function testCantExtractSelectFieldsWithoutFields()
    {
        $this->selectBuilder = $this
            ->getMockBuilder('Sensorario\QueryBuilder\SelectBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadata = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Objects\MetaData')
            ->disableOriginalConstructor()
            ->getMock();

        $extractor = new Extractor(
            $this->selectBuilder,
            $this->metadata
        );

        $extractor->extractSelectFields();
    }

    public function testExtractSelectFieldsWithoutFields()
    {
        $this->selectBuilder = $this
            ->getMockBuilder('Sensorario\QueryBuilder\SelectBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->selectBuilder->expects($this->once())
            ->method('setTable')
            ->with('tab');
        $this->selectBuilder->expects($this->once())
            ->method('addFields');
        $this->selectBuilder->expects($this->once())
            ->method('getFields')
            ->willReturn([
                'tab.foo tab_foo',
                'tab.bar tab_bar',
            ]);

        $this->metadata = $this
            ->getMockBuilder('Sensorario\QueryBuilder\Objects\MetaData')
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadata->expects($this->once())
            ->method('getTable')
            ->willReturn('tab');

        $extractor = new Extractor(
            $this->selectBuilder,
            $this->metadata
        );

        $extractor->setFields([
            'foo',
            'bar',
        ]);

        $extracted = $extractor->extractSelectFields();

        $this->assertEquals(
            'tab.foo tab_foo, tab.bar tab_bar',
            $extracted
        );
    }
}
