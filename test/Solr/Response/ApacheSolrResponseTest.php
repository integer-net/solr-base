<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */

namespace IntegerNet\Solr\Response;


class ApacheSolrResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testClassAliasForBackwardsCompatibility()
    {
        $this->assertInstanceOf(\IntegerNet\Solr\Resource\ResponseDecorator::class,
            new ApacheSolrResponse($this->createApacheSolrResponse('{}'))
        );
        $this->assertInstanceOf(ApacheSolrResponse::class,
            new \IntegerNet\Solr\Resource\ResponseDecorator($this->createApacheSolrResponse('{}'))
        );
    }

    public function testDocuments()
    {
        $response = new ApacheSolrResponse($this->createApacheSolrResponse(\json_encode([
            'response' => [
                'docs' => [
                    [
                        'product_id' => 2,
                    ],
                    [
                        'product_id' => 3,
                    ],
                ]
            ]
        ])));
        $expectedDocuments = [
            ['product_id' => 2],
            ['product_id' => 3],
        ];
        $documents = $response->documents();
        $this->assertInstanceOf(DocumentCollection::class, $documents);
        $this->assertCount(2, $documents);
        $this->assertEquals($expectedDocuments, $documents->asArray());
        $firstDocument = \iterator_to_array($documents)[0];
        $this->assertEquals(new Field('product_id', 2, null), $firstDocument->field('product_id'), 'Document field');
        $this->assertEquals(new Field('bielefeld', null, null), $firstDocument->field('bielefeld'), 'Nonexistent document field');
    }

    /**
     * @param $statusCode
     * @param $contentType
     * @param $responseBody
     * @return \Apache_Solr_Response
     */
    private function createApacheSolrResponse($responseBody, $statusCode = 200, $contentType = 'application/json')
    {
        return new \Apache_Solr_Response(
            new \Apache_Solr_HttpTransport_Response($statusCode, $contentType, $responseBody)
        );
    }
}
