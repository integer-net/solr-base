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

final class ProductIdChunks extends \ArrayIterator
{
    /**
     * @return ProductIdChunk
     */
    public function current()
    {
        return parent::current();
    }

    /**
     * @param int[] $allProductIds Product ids to be chunked
     * @param ProductAssociation[] $associations Product associations. Associated products should be together in a chunk
     * @param int $chunkSize Maximum number of products per chunk
     * @return ProductIdChunks
     */
    public static function withAssociationsTogether($allProductIds, $associations, $chunkSize)
    {
        $productIdChunks = new self;
        $currentChunk = new ProductIdChunk();
        $productIdChunks->append($currentChunk);
        foreach ($allProductIds as $key => $productId) {
            $parentAndChildrenProductCount = 1;
            if (isset($associations[$productId])) {
                $parentAndChildrenProductCount += sizeof($associations[$productId]);
            }
            if ($currentChunk->getSize() > 0 && $currentChunk->getSize() + $parentAndChildrenProductCount > $chunkSize) {
                $currentChunk = new ProductIdChunk();
                $productIdChunks->append($currentChunk);
            }
            if (isset($associations[$productId])) {
                $currentChunk->addProductIds($productId, $associations[$productId]->childrenIds());
            } else {
                $currentChunk->addProductIds($productId);
            }
        }
        return $productIdChunks;
    }

    /**
     * @return int Total number of products in all chunks (with associations)
     */
    public function totalCount()
    {
        return array_reduce(
            $this->getArrayCopy(),
            function ($count, ProductIdChunk $chunk) {
                return $count + count($chunk);
            },
            0
        );
    }
}