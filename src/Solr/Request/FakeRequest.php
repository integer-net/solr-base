<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */

namespace IntegerNet\Solr\Request;

use IntegerNet\Solr\Response\ApacheSolrResponse as ResponseDecorator;
use IntegerNet\Solr\Response\Response;

/**
 * Fake Request object to be used in integration tests
 */
class FakeRequest implements Request
{
    private $responseBody;

    /**
     * @param string $responseBody
     */
    public function __construct($responseBody = '{}')
    {
        $this->responseBody = $responseBody;
    }

    /**
     * @param string[] $activeFilterAttributeCodes
     * @return Response
     */
    public function doRequest($activeFilterAttributeCodes = array())
    {
        return new ResponseDecorator(
            new \Apache_Solr_Response(
                new \Apache_Solr_HttpTransport_Response(200, 'application/json', $this->responseBody)
            )
        );
    }

}