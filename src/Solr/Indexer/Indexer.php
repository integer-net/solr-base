<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2017 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */

namespace IntegerNet\Solr\Indexer;

interface Indexer
{
    /**
     * Add/update entities in index
     *
     * @param string[]|null $entityIds IDs to reindex, depends on content type of the concrete indexer. Null for "all"
     * @param bool $emptyIndex If index should be cleared before writing
     * @param int[]|null $restrictToStoreIds Store IDs to reindex. Null for "all"
     * @param int|null $sliceId Number of slice for partial reindexing. Null for no partial reindexing
     * @param int|null $totalNumberSlices Number of slices. The entities are divided into this many slices.
     */
    public function reindex(
        $entityIds = null,
        $emptyIndex = false,
        $restrictToStoreIds = null,
        $sliceId = null,
        $totalNumberSlices = null
    );

    /**
     * Delete given entities from index
     *
     * @param string[] $entityIds IDs to delete, depends on content type of the concrete indexer.
     */
    public function deleteIndex($entityIds);

    /**
     * Clear index for content type of concrete indexer and given store ID
     *
     * @param int $storeId
     */
    public function clearIndex($storeId);
}