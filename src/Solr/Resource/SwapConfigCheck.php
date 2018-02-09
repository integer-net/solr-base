<?php

namespace IntegerNet\Solr\Resource;

use IntegerNet\Solr\Exception;
use IntegerNet\Solr\Implementor\Config;

class SwapConfigCheck
{
    /**
     * @param Config[] $allStoresConfig Store config by store ID
     * @param null $restrictStoreIdsToSwap If not null, check if it's possible to swap cores for these stores together
     * @throws Exception
     */
    public function checkSwapCoresConfiguration(array $allStoresConfig, $restrictStoreIdsToSwap = null)
    {
        $swapCoreConfig = new SwapCoreConfig($allStoresConfig);

        $this->checkIfSwapCoreIsUsedAsMainCore(
            $swapCoreConfig->getMainCoreIds(),
            $swapCoreConfig->getUsedSwapCoreIds()
        );
        $this->checkIfSwappingIsActiveForAllStoresWithSameCore(
            $swapCoreConfig->getCoreIdsToSwap(),
            $swapCoreConfig->getCoreIdsNotToSwap()
        );

        foreach ($swapCoreConfig->getSwapCoreNamesByCoreAndStore() as $swapCoreNamesByStore) {
            $this->checkIfDifferentSwapCoresAreUsed($swapCoreNamesByStore);
            if (null !== $restrictStoreIdsToSwap) {
                $this->checkIfStoresWithSharedConfigAreSwappedTogether($restrictStoreIdsToSwap,
                    array_keys($swapCoreNamesByStore)
                );
            }
        }
    }

    private function checkIfStoresWithSharedConfigAreSwappedTogether($restrictStoreIdsToSwap, $storeIdsWithSharedConfig)
    {
        if (
            count(array_intersect($storeIdsWithSharedConfig, $restrictStoreIdsToSwap))
            && count(array_diff($storeIdsWithSharedConfig, $restrictStoreIdsToSwap))
        ) {
            throw new Exception(
                'Call Error: All Stores using the same Swap Configuration must be reindexed at the same Time.'
            );
        }
    }

    private function checkIfDifferentSwapCoresAreUsed($swapCoreNamesByStore)
    {
        if (count(array_unique($swapCoreNamesByStore)) > 1) {
            throw new Exception(
                'Configuration Error: A Core must swap with the same Core for all Store Views using it.'
            );
        }
    }

    private function checkIfSwappingIsActiveForAllStoresWithSameCore($coresToSwap, $coresNotToSwap)
    {
        if (count(array_intersect($coresToSwap, $coresNotToSwap))) {
            throw new Exception(
                'Configuration Error: Activate Core Swapping for all Store Views using the same Solr Core.'
            );
        }
    }

    private function checkIfSwapCoreIsUsedAsMainCore($mainCoreIds, $usedSwapCoreIds)
    {
        if (count(array_intersect($mainCoreIds, $usedSwapCoreIds))) {
            throw new Exception('Configuration Error: A Swap Core must not be used as Main Core in other Store View.');
        }
    }

}