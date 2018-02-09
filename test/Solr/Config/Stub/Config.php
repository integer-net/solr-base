<?php

namespace IntegerNet\Solr\Config\Stub;

use IntegerNet\Solr\Config\AutosuggestConfig;
use IntegerNet\Solr\Config\CategoryConfig;
use IntegerNet\Solr\Config\CmsConfig;
use IntegerNet\Solr\Config\FuzzyConfig;
use IntegerNet\Solr\Config\GeneralConfig;
use IntegerNet\Solr\Config\IndexingConfig;
use IntegerNet\Solr\Config\ResultsConfig;
use IntegerNet\Solr\Config\ServerConfig;
use IntegerNet\Solr\Config\StoreConfig;
use IntegerNet\Solr\Implementor\Config as ConfigInterface;

class Config implements ConfigInterface
{
    /**
     * @var StoreConfigBuilder
     */
    private $storeConfig;
    /**
     * @var GeneralConfigBuilder
     */
    private $generalConfig;
    /**
     * @var ServerConfigBuilder
     */
    private $serverConfig;
    /**
     * @var IndexingConfigBuilder
     */
    private $indexingConfig;
    /**
     * @var AutosuggestConfigBuilder
     */
    private $autosuggestConfig;
    /**
     * @var FuzzyConfigBuilder
     */
    private $fuzzySearchConfig;
    /**
     * @var FuzzyConfigBuilder
     */
    private $fuzzyAutosuggestConfig;
    /**
     * @var ResultConfigBuilder
     */
    private $resultsConfig;
    /**
     * @var CategoryConfigBuilder
     */
    private $categoryConfig;
    /**
     * @var CmsConfigBuilder
     */
    private $cmsConfig;

    public function __construct()
    {
        $this->storeConfig = StoreConfigBuilder::defaultConfig();
        $this->generalConfig = GeneralConfigBuilder::defaultConfig();
        $this->serverConfig = ServerConfigBuilder::defaultConfig();
        $this->indexingConfig = IndexingConfigBuilder::defaultConfig();
        $this->autosuggestConfig = AutosuggestConfigBuilder::defaultConfig();
        $this->fuzzySearchConfig = FuzzyConfigBuilder::defaultConfig();
        $this->fuzzyAutosuggestConfig = FuzzyConfigBuilder::defaultConfig();
        $this->resultsConfig = ResultConfigBuilder::defaultConfig();
        $this->categoryConfig = CategoryConfigBuilder::defaultConfig();
        $this->cmsConfig = CmsConfigBuilder::defaultConfig();
    }

    public static function defaultConfig()
    {
        return new static();
    }

    /**
     * @param StoreConfigBuilder $storeConfig
     * @return Config
     */
    public function withStoreConfig(StoreConfigBuilder $storeConfig)
    {
        $config = clone $this;
        $config->storeConfig = $storeConfig;
        return $config;
    }

    /**
     * @param GeneralConfigBuilder $generalConfig
     * @return Config
     */
    public function withGeneralConfig(GeneralConfigBuilder $generalConfig)
    {
        $config = clone $this;
        $config->generalConfig = $generalConfig;
        return $config;
    }

    /**
     * @param ServerConfigBuilder $serverConfig
     * @return Config
     */
    public function withServerConfig(ServerConfigBuilder $serverConfig)
    {
        $config = clone $this;
        $config->serverConfig = $serverConfig;
        return $config;
    }

    /**
     * @param IndexingConfigBuilder $indexingConfig
     * @return Config
     */
    public function withIndexingConfig(IndexingConfigBuilder $indexingConfig)
    {
        $config = clone $this;
        $config->indexingConfig = $indexingConfig;
        return $config;
    }

    /**
     * @param AutosuggestConfigBuilder $autosuggestConfig
     * @return Config
     */
    public function withAutosuggestConfig(AutosuggestConfigBuilder $autosuggestConfig)
    {
        $config = clone $this;
        $config->autosuggestConfig = $autosuggestConfig;
        return $config;
    }

    /**
     * @param FuzzyConfigBuilder $fuzzySearchConfig
     * @return Config
     */
    public function withFuzzySearchConfig(FuzzyConfigBuilder $fuzzySearchConfig)
    {
        $config = clone $this;
        $config->fuzzySearchConfig = $fuzzySearchConfig;
        return $config;
    }

    /**
     * @param FuzzyConfigBuilder $fuzzyAutosuggestConfig
     * @return Config
     */
    public function withFuzzyAutosuggestConfig(FuzzyConfigBuilder $fuzzyAutosuggestConfig)
    {
        $config = clone $this;
        $config->fuzzyAutosuggestConfig = $fuzzyAutosuggestConfig;
        return $config;
    }

    /**
     * @param ResultConfigBuilder $resultsConfig
     * @return Config
     */
    public function withResultsConfig(ResultConfigBuilder $resultsConfig)
    {
        $config = clone $this;
        $config->resultsConfig = $resultsConfig;
        return $config;
    }

    /**
     * @param CategoryConfigBuilder $categoryConfig
     * @return Config
     */
    public function withCategoryConfig(CategoryConfigBuilder $categoryConfig)
    {
        $config = clone $this;
        $config->categoryConfig = $categoryConfig;
        return $config;
    }

    /**
     * @param CmsConfigBuilder $cmsConfig
     * @return Config
     */
    public function withCmsConfig(CmsConfigBuilder $cmsConfig)
    {
        $config = clone $this;
        $config->cmsConfig = $cmsConfig;
        return $config;
    }

    /**
     * @return StoreConfig
     */
    public function getStoreConfig()
    {
        return $this->storeConfig->build();
    }

    /**
     * @return GeneralConfig
     */
    public function getGeneralConfig()
    {
        return $this->generalConfig->build();
    }

    /**
     * @return ServerConfig
     */
    public function getServerConfig()
    {
        return $this->serverConfig->build();
    }

    /**
     * @return IndexingConfig
     */
    public function getIndexingConfig()
    {
        return $this->indexingConfig->build();
    }

    /**
     * @return AutosuggestConfig
     */
    public function getAutosuggestConfig()
    {
        return $this->autosuggestConfig->build();
    }

    /**
     * @return FuzzyConfig
     */
    public function getFuzzySearchConfig()
    {
        return $this->fuzzySearchConfig->build();
    }

    /**
     * @return FuzzyConfig
     */
    public function getFuzzyAutosuggestConfig()
    {
        return $this->fuzzyAutosuggestConfig->build();
    }

    /**
     * @return ResultsConfig
     */
    public function getResultsConfig()
    {
        return $this->resultsConfig->build();
    }

    /**
     * @return CategoryConfig
     */
    public function getCategoryConfig()
    {
        return $this->categoryConfig->build();
    }

    /**
     * @return CmsConfig
     */
    public function getCmsConfig()
    {
        return $this->cmsConfig->build();
    }

}