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
    /**
     * @return Request
     */
    public function createRequest()
    {
        return $this->requests[] = new FakeRequest();
    }

    public function getLastRequest()
    {
        return end($this->requests);
    }

}