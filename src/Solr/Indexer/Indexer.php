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

use IntegerNet\Solr\Indexer\Progress\ProgressHandler;

interface Indexer
{
    /**
     * Adds a callback for progress updates
     *
     * @param ProgressHandler $handler
     */
    public function addProgressHandler(ProgressHandler $handler);

    /**
     * Add/update entities in index
     *
     * The sliceId/totalNumberSlices parameters are deprecated, they cannot be used together with entityIds/emptyIndex.
     * Use reindexSlice() instead to reindex slices.
     *
     * @param string[]|null $entityIds IDs to reindex, depends on content type of the concrete indexer. Null for "all"
     * @param bool $emptyIndex If index should be cleared before writing
     * @param int[]|null $restrictToStoreIds Store IDs to reindex. Null for "all"
     * @param int|null $sliceId Number of slice for partial reindexing. Null for no partial reindexing.
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
     * @param Slice $slice
     * @return mixed
     */
    public function reindexSlice(Slice $slice, $restrictToStoreIds = null);

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

    /**
     * Check if swap configuration is valid to reindex given stores
     * (e.g. all stores with same swap configuration must be reindexed at once)
     *
     * @param $restrictToStoreIds
     */
    public function checkSwapCoresConfiguration($restrictToStoreIds);

    /**
     * Swap current core with shadow core (for all given stores)
     *
     * @param null|int[] $restrictToStoreIds
     */
    public function swapCores($restrictToStoreIds);

    /**
     * Use the shadow core for subsequent indexing
     */
    public function activateSwapCore();

    /**
     * Do not use the shadow core for subsequent indexing
     */
    public function deactivateSwapCore();
}