<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */
namespace IntegerNet\Solr\Response;

interface Response
{
    /**
     * Returns raw JSON response from Solr
     *
     * @return string
     */
    public function getRawResponse();
    /**
     * @return DocumentCollection
     */
    public function documents();
    /**
     * Returns new response instance, with merged results from both responses
     *
     * @param Response $other
     * @param int $pageSize
     * @return Response
     */
    public function merge(Response $other, $pageSize);
    /**
     * Returns new response instance with slice from item number $from until item number $from + $length
     *
     * @param $from
     * @param $length
     * @return Response
     */
    public function slice($from, $length);

}
\class_alias(Response::class, \IntegerNet\Solr\Resource\SolrResponse::class, false);
