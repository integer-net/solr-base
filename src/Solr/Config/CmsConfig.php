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
     * @param bool $active
     * @param bool $useInSearchResults
     * @param int $maxNumberResults
     */
    public function __construct($active, $useInSearchResults, $maxNumberResults)
    {
        $this->active = $active;
        $this->useInSearchResults = $useInSearchResults;
        $this->maxNumberResults = $maxNumberResults;
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

}