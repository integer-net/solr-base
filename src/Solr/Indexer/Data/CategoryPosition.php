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

final class CategoryPosition extends \ArrayObject
{
    /**
     * @var int
     */
    private $categoryId;
    /**
     * @var int
     */
    private $position;

    /**
     * @param int $position
     * @param int $categoryId
     */
    public function __construct($position, $categoryId)
    {
        $this->position = $position;
        $this->categoryId = $categoryId;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @deprecated use getter instead of array access
     * @param mixed $index
     * @return mixed
     */
    public function offsetGet($index)
    {
        if ($index === 'category_id') {
            return $this->getCategoryId();
        }
        if ($index === 'position') {
            return $this->getPosition();
        }
        return parent::offsetGet($index);
    }

}