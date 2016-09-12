<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */
namespace IntegerNet\Solr\Resource;
use IntegerNet\Solr\Response\Response;

/**
 * @deprecated alias for BC compatibility
 *
 * autoload real class where alias is defined
 */
\class_exists(Response::class);

if (false) {
    /** @deprecated alias for \IntegerNet\Solr\Response\Response */
    interface SolrResponse extends Response {}
}