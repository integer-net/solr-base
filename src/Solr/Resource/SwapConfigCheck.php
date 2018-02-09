<?php

namespace IntegerNet\Solr\Resource;

use IntegerNet\Solr\Exception;
use IntegerNet\Solr\Implementor\Config;

class SwapConfigCheck
{
    /**
     * @param Config[] $storesConfig Store config by store ID
     * @param null $restrictStoreIdsToSwap If not null, check if it's possible to swap cores for these stores together
     * @throws Exception
     */
    public function checkSwapCoresConfiguration(array $storesConfig, $restrictStoreIdsToSwap = null)
    {
        $coresToSwap = array();
        $coresNotToSwap = array();
        $swapCoreNames = array();

        foreach ($storesConfig as $storeId => $storeConfig) {

            if ($storeId == 0) {
                continue;
            }

            /** @var Config $storeConfig */
            $solrServerInfo = $storeConfig->getServerConfig()->getServerInfo();

            if (!$storeConfig->getGeneralConfig()->isActive()) {
                continue;
            }

            if ($storeConfig->getIndexingConfig()->isSwapCores()) {
                $coresToSwap[$storeId] = $solrServerInfo;
                $swapCoreNames[$solrServerInfo][$storeId] = $storeConfig->getServerConfig()->getSwapCore();
            } else {
                $coresNotToSwap[$storeId] = $solrServerInfo;
            }
        }

        if (count(array_intersect($coresToSwap, $coresNotToSwap))) {
            throw new Exception(
                'Configuration Error: Activate Core Swapping for all Store Views using the same Solr Core.'
            );
        }

        foreach ($swapCoreNames as $swapCoreNamesByCore) {
            if (count(array_unique($swapCoreNamesByCore)) > 1) {
                throw new Exception(
                    'Configuration Error: A Core must swap with the same Core for all Store Views using it.'
                );
            }
            if (null !== $restrictStoreIdsToSwap) {
                if (count(array_intersect($restrictStoreIdsToSwap, array_keys($swapCoreNamesByCore)))) {
                    if (count(array_diff(array_keys($swapCoreNamesByCore), $restrictStoreIdsToSwap))) {
                        throw new Exception(
                            'Call Error: All Stores using the same Swap Configuration must be reindexed at the same Time.'
                        );
                    }
                }
            }
        }
    }
}