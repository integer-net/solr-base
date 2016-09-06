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

interface Document
{
    /**
     * @param $name
     * @return Field
     */
    public function field($name);

    /**
     * Returns field values as associative array
     *
     * @return mixed[]
     */
    public function asArray();
}