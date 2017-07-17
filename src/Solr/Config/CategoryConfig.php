<?php
namespace IntegerNet\Solr\Config;
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */
final class CategoryConfig
{
    const FILTER_POSITION_DEFAULT = 0;
    const FILTER_POSITION_LEFT = 1;
    const FILTER_POSITION_TOP = 2;

    /**
     * @var bool
     */
    private $active;
    /**
     * @var int
     */
    private $filterPosition;
    /**
     * @var bool
     */
    private $indexerActive;

    /**
     * @var bool
     */
    private $useInSearchResults;

    /**
     * @var int
     */
    private $maxNumberResults;

    /**
     * @var bool
     */
    private $isFuzzyActive;

    /**
     * @var float
     */
    private $fuzzySensitivity;
    /**
     * @var bool
     */
    private $showOutOfStock;

    /**
     * @param bool $active
     * @param int $filterPosition
     * @param bool $indexerActive
     * @param bool $useInSearchResults
     * @param int $maxNumberResults
     * @param bool $isFuzzyActive
     * @param float $fuzzySensitivity
     */
    public function __construct($active, $filterPosition, $indexerActive, $useInSearchResults, $maxNumberResults, $isFuzzyActive, $fuzzySensitivity, $showOutOfStock)
    {
        $this->active = $active;
        $this->filterPosition = $filterPosition;
        $this->indexerActive = $indexerActive;
        $this->useInSearchResults = $useInSearchResults;
        $this->maxNumberResults = $maxNumberResults;
        $this->isFuzzyActive = $isFuzzyActive;
        $this->fuzzySensitivity = $fuzzySensitivity;
        $this->showOutOfStock = $showOutOfStock;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return int
     */
    public function getFilterPosition()
    {
        return $this->filterPosition;
    }

    /**
     * @return boolean
     */
    public function isIndexerActive()
    {
        return $this->indexerActive;
    }

    /**
     * @return boolean
     */
    public function canUseInSearchResults()
    {
        return $this->useInSearchResults;
    }

    /**
     * @return int
     */
    public function getMaxNumberResults()
    {
        return $this->maxNumberResults;
    }

    /**
     * @return bool
     */
    public function isFuzzyActive()
    {
        return $this->isFuzzyActive;
    }

    /**
     * @return float
     */
    public function getFuzzySensitivity()
    {
        return $this->fuzzySensitivity;
    }

    /**
     * @return bool
     */
    public function isShowOutOfStock()
    {
        return $this->showOutOfStock;
    }
}