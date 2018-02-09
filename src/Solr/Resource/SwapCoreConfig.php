<?php

namespace IntegerNet\Solr\Resource;
use IntegerNet\Solr\Implementor\Config;

/**
 * Extracted global information about used swap cores from store configurations
 */
class SwapCoreConfig
{
    /**
     * @var array|Config[]
     */
    private $allStoresConfig;
    /**
     * @var string[]
     */
    private $mainCoreIds;
    /**
     * @var string[]
     */
    private $usedSwapCoreIds;
    /**
     * @var string[]
     */
    private $coreIdsToSwap;
    /**
     * @var string[]
     */
    private $coreIdsNotToSwap;
    /**
     * @var string[][]
     */
    private $swapCoreNamesByCoreAndStore;
    /**
     * @var bool
     */
    private $extracted = false;

    /**
     * @param Config[] $allStoresConfig
     */
    public function __construct(array $allStoresConfig)
    {
        $this->allStoresConfig = $allStoresConfig;
    }

    /**
     * @return array|Config[]
     */
    public function getAllStoresConfig()
    {
        if (!$this->extracted) {
            $this->extract();
        }
        return $this->allStoresConfig;
    }

    /**
     * @return string[]
     */
    public function getMainCoreIds()
    {
        if (!$this->extracted) {
            $this->extract();
        }
        return $this->mainCoreIds;
    }

    /**
     * @return string[]
     */
    public function getUsedSwapCoreIds()
    {
        if (!$this->extracted) {
            $this->extract();
        }
        return $this->usedSwapCoreIds;
    }

    /**
     * @return string[]
     */
    public function getCoreIdsToSwap()
    {
        if (!$this->extracted) {
            $this->extract();
        }
        return $this->coreIdsToSwap;
    }

    /**
     * @return string[]
     */
    public function getCoreIdsNotToSwap()
    {
        if (!$this->extracted) {
            $this->extract();
        }
        return $this->coreIdsNotToSwap;
    }

    /**
     * @return string[][]
     */
    public function getSwapCoreNamesByCoreAndStore()
    {
        if (!$this->extracted) {
            $this->extract();
        }
        return $this->swapCoreNamesByCoreAndStore;
    }

    private function extract()
    {
        $this->mainCoreIds = [];
        $this->usedSwapCoreIds = [];
        $this->coreIdsToSwap = [];
        $this->coreIdsNotToSwap = [];
        $this->swapCoreNamesByCoreAndStore = [];

        foreach ($this->allStoresConfig as $storeId => $storeConfig) {

            if ($storeId == 0 || !$storeConfig->getGeneralConfig()->isActive()) {
                continue;
            }

            /** @var Config $storeConfig */
            $coreName = $storeConfig->getServerConfig()->getCore();
            $swapCoreName = $storeConfig->getServerConfig()->getSwapCore();
            $coreId = $storeConfig->getServerConfig()->getServerInfo();
            $swapCoreId = substr_replace(
                $coreId,
                $swapCoreName,
                -\strlen($coreName)
            );

            $this->mainCoreIds[] = $coreId;
            if ($storeConfig->getIndexingConfig()->isSwapCores()) {
                $this->coreIdsToSwap[] = $coreId;
                $this->usedSwapCoreIds[] = $swapCoreId;
                $this->swapCoreNamesByCoreAndStore[$coreId][$storeId] = $swapCoreName;
            } else {
                $this->coreIdsNotToSwap[$storeId] = $coreId;
            }
        }
        $this->extracted = true;
    }
}