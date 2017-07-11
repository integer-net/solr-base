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

use IntegerNet\Solr\Config\CmsConfig;;

class CmsConfigBuilder
{
    /*
     * Default values
     */
    private $active = false;
    private $useInSearchResults = true;
    private $maxNumberResults = 10;
    private $isFuzzyActive = true;
    private $fuzzySensitivity = 0.8;

    private function __construct()
    {
    }
    public static function defaultConfig()
    {
        return new static;
    }

   public function build()
    {
        return new CmsConfig($this->active, $this->useInSearchResults, $this->maxNumberResults, $this->isFuzzyActive,
            $this->fuzzySensitivity);
    }
}