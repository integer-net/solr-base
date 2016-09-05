<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */

namespace IntegerNet\Solr\Request;


final class FakeRequestFactory implements RequestFactory
{
    /**
     * @var Request[]
     */
    private $requests = [];
    private $response = '{}';
    /**
     * @return Request
     */
    public function createRequest()
    {
        return $this->requests[] = new FakeRequest($this->response);
    }

    public function getLastRequest()
    {
        return end($this->requests);
    }

    /**
     * @param string $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

}