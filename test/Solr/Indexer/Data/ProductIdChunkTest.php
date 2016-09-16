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


class ProductIdChunkTest extends \PHPUnit_Framework_TestCase
{
    public function testAllIds()
    {
        $chunk = new ProductIdChunk();
        $chunk->addProductIds(1, [11,12]);
        $chunk->addProductIds(3, [33,35]);
        $chunk->addProductIds(11);

        $this->assertEquals([1,3,11,12,33,35], $chunk->getAllIds(), 'getAllIds', 0.0, 10, true);
    }
    public function testAssociations()
    {
        $chunk = new ProductIdChunk();
        $chunk->addProductIds(1, [11,12]);
        $chunk->addProductIds(3, [33,35]);

        $this->assertEquals([33,35], $chunk->childrenOf(3));
    }
}
