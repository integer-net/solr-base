<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */
namespace IntegerNet\Solr\Config\Stub;

use IntegerNet\Solr\Config\CategoryConfig;;

class CategoryConfigBuilder
{
    /*
     * Default values
     */
    private $active = false;
    private $filterPosition = CategoryConfig::FILTER_POSITION_DEFAULT;
    private $indexerActive = false;
    private $useInSearchResults = true;
    private $maxNumberResults = 3;
    private $isFuzzyActive = true;
    private $fuzzySensitivity = 0.8;
    private $showOutOfStock = true;

    private function __construct()
    {
    }
    public static function defaultConfig()
    {
        return new static;
    }

    /**
     * @param bool $active
     * @return $this
     */
    public function withActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @param int $filterPosition
     * @return $this
     */
    public function withFilterPosition($filterPosition)
    {
        $this->filterPosition = $filterPosition;
        return $this;
    }

    /**
     * @param bool $indexerActive
     * @return $this
     */
    public function withIndexerActive($indexerActive)
    {
        $this->indexerActive = $indexerActive;
        return $this;
    }

    public function build()
    {
        return new CategoryConfig($this->active, $this->filterPosition, $this->indexerActive, $this->useInSearchResults,
            $this->maxNumberResults, $this->isFuzzyActive, $this->fuzzySensitivity, $this->showOutOfStock);
    }
}