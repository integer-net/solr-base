<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */
namespace IntegerNet\Solr\Response;

use Countable;
use Iterator;

interface DocumentCollection extends Iterator, Countable
{
    /**
     * @return Document
     */
    public function current();

    /**
     * Returns each document as associative array
     *
     * @return mixed[][]
     */
    public function asArray();
}