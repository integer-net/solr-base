<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */
namespace IntegerNet\Solr\Indexer\Data;

class CategoryPositionCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataInputArray
     * @param $inputArray
     */
    public function testInstantiationFromArray($inputArray)
    {
        $collection = CategoryPositionCollection::fromArray($inputArray);
        $this->assertCount(\count($inputArray), $collection);
        \reset($inputArray);
        foreach ($collection as $categoryPosition) {
            /** @var CategoryPosition $categoryPosition */
            $inputRow = \current($inputArray);
            $this->assertEquals($inputRow['category_id'], $categoryPosition->getCategoryId());
            $this->assertEquals($inputRow['position'], $categoryPosition->getPosition());
            $this->assertEquals($inputRow['category_id'], $categoryPosition['category_id'], 'backwards compatible array syntax');
            $this->assertEquals($inputRow['position'], $categoryPosition['position'], 'backwards compatible array syntax');
            \next($inputArray);
        }
    }
    public static function dataInputArray()
    {
        return [
            [
                'input_array' => [
                    ['category_id' => 3, 'position' => 10],
                    ['category_id' => 4, 'position' => 0],
                    ['category_id' => 20, 'position' => 17],
                ],
            ]
        ];
    }
}