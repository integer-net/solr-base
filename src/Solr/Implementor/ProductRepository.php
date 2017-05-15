<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */
namespace IntegerNet\Solr\Implementor;

use IntegerNet\Solr\Indexer\Data\ProductIdChunks;

interface ProductRepository
{
    /**
     * Return product iterator which may implement lazy loading but must ensure that given chunks are loaded together
     *
     * @param int $storeId
     * @param ProductIdChunks $chunks
     * @return PagedProductIterator
     */
    public function getProductsInChunks($storeId, ProductIdChunks $chunks);

    /**
     * @param null|int $sliceId
     * @param null|int $totalNumberSlices
     * @return int[]
     */
    public function getAllProductIds($sliceId = null, $totalNumberSlices = null);

    /**
     * @param null|int[] $productIds
     * @return \IntegerNet\Solr\Indexer\Data\ProductAssociation[] An array with parent_id as key and association metadata as value
     */
    public function getProductAssociations($productIds);

}