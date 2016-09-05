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


use IntegerNet\Solr\Resource\SolrResponse;

class FakeRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testItReturnsSolrResponse()
    {
        $request = new FakeRequest();
        $response = $request->doRequest();
        $this->assertInstanceOf(SolrResponse::class, $response);
    }
    public function testItCanReturnPredefinedJsonResponse()
    {
        $dummySku = 'abc';
        $request = new FakeRequest(\json_encode([
            'response' => [
                'docs' => [
                    ['sku' => $dummySku]
                ]
            ]
        ]));
        $response = $request->doRequest();
        $this->assertEquals($dummySku, $response->response->docs[0]->sku);
    }
}
