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
final class CmsConfig
{
    /**
     * @var bool
     */
    private $active;

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
     * @param bool $active
     * @param bool $useInSearchResults
     * @param int $maxNumberResults
     * @param bool $isFuzzyActive
     * @param float $fuzzySensitivity
     */
    public function __construct($active, $useInSearchResults, $maxNumberResults, $isFuzzyActive, $fuzzySensitivity)
    {
        $this->active = $active;
        $this->useInSearchResults = $useInSearchResults;
        $this->maxNumberResults = $maxNumberResults;
        $this->isFuzzyActive = $isFuzzyActive;
        $this->fuzzySensitivity = $fuzzySensitivity;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
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

}