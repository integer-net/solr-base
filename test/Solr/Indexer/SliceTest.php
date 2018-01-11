<?php

namespace IntegerNet\Solr\Indexer;

use PHPUnit\Framework\TestCase;

class SliceTest extends TestCase
{
    /**
     * @dataProvider dataValidExpression
     */
    public function testCanBeInstantiatedFromValidExpression($expression, $expectedNumber, $expectedTotalNumber)
    {
        $slice = Slice::fromExpression($expression);
        $this->assertEquals(new Slice($expectedNumber, $expectedTotalNumber), $slice);
    }

    public static function dataValidExpression()
    {
        return [
            '1/5' => ['1/5', 1, 5],
            '5/5' => ['5/5', 5, 5],
            '1/1' => ['1/1', 1, 1],
        ];
    }
    /**
     * @dataProvider dataInvalidExpression
     */
    public function testCannotBeInstantiatedFromInvalidExpression($expression)
    {
        $this->expectException(\InvalidArgumentException::class);
        Slice::fromExpression($expression);
    }

    public static function dataInvalidExpression()
    {
        return [
            '0/5' => ['0/5'],
            '6/5' => ['6/5'],
            '' => [''],
            '1/' => ['1/'],
            '/2' => ['/2'],
            '1' => ['1'],
            'foo/bar' => ['foo/bar'],
            '1/2/3' => ['1/2/3'],
            '1.5/5' => ['1.5/5'],
            '1/2.5' => ['1/2.5'],
        ];
    }
}