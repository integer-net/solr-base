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


final class ProductAssociation implements \Countable
{
    /** @var int */
    private $parentId;
    /** @var int[] */
    private $childrenIds;

    /**
     * @param int $parentId
     * @param int[] $childrenIds
     */
    public function __construct($parentId, array $childrenIds = [])
    {
        $this->parentId = $parentId;
        $this->childrenIds = $childrenIds;
    }

    public function parentId()
    {
        return $this->parentId;
    }
    public function childrenIds()
    {
        return $this->childrenIds;
    }

    public function count()
    {
        return \count($this->childrenIds());
    }

}