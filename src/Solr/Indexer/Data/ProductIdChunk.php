<?php
namespace IntegerNet\Solr\Indexer\Data;
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */
final class ProductIdChunk implements \Countable
{
    /** @var  int[] */
    private $parentIds = array();

    /** @var  int[][] */
    private $childrenIds = array();

    /** @var ProductAssociation[] */
    private $associations = array();

    private $size = 0;

    /**
     */
    public function __construct()
    {
        $this->parentIds = array();
        $this->childrenIds = array();
    }

    /**
     * @return int Approximate size based on added products
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return int Real size without duplicates
     */
    public function count()
    {
        return count($this->getAllIds());
    }

    /**
     * @return int[]
     */
    public function getAllIds()
    {
        $productIds = $this->parentIds;
        foreach ($this->childrenIds as $parentId => $childrenIds) {
            foreach ($childrenIds as $childId) {
                $productIds[] = $childId;
            }
        }
        return array_unique($productIds);
    }

    /**
     * @param int $parentId
     * @param int[] $childrenIds
     */
    public function addProductIds($parentId, $childrenIds = array())
    {
        $this->associations[$parentId] = new ProductAssociation($parentId, $childrenIds);
        //TODO derive all data from keys and values of $this->associations ?
        $this->parentIds[] = $parentId;
        if (sizeof($childrenIds)) {
            $this->childrenIds[$parentId] = $childrenIds;
        }
        $this->size += sizeof($childrenIds) + 1;
    }

    /**
     * @param $parentId
     * @return int[]
     */
    public function childrenOf($parentId)
    {
        return isset($this->childrenIds[$parentId]) ? $this->childrenIds[$parentId] : [];
    }
}