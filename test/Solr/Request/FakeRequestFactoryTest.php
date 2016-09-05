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


class FakeRequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testItCreatesFakeRequest()
    {
        $factory = new FakeRequestFactory();
        $this->assertInstanceOf(FakeRequest::class, $factory->createRequest());
    }
    public function testAccessToCreatedFakeRequests()
    {
        $factory = new FakeRequestFactory();
        $request = $factory->createRequest();
        $this->assertSame($request, $factory->getLastRequest());
    }
}
