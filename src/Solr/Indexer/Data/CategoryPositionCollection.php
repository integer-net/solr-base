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

class CategoryPositionCollection extends \ArrayIterator
{
    /**
     * @param array $data Array of items in the form ['category_id' => int, 'position' => int]
     * @return CategoryPositionCollection
     */
    public static function fromArray(array $data)
    {
        return new static(\array_map(function($row) {
            return new CategoryPosition($row['position'], $row['category_id']);
        }, $data));
    }

    /**
     * @return CategoryPosition
     */
    public function current()
    {
        return parent::current();
    }

}