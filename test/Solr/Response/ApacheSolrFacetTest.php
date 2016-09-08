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


class ApacheSolrFacetTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiationFromField()
    {
        $field = 'foobar_facet';
        $counts = ['foo' => 7, 'bar' => 2];
        $facet = ApacheSolrFacet::fromField($field, $counts);
        $this->assertEquals('foobar', $facet->name());
        $this->assertEquals([
            new FacetCount('foo', 7),
            new FacetCount('bar', 2)
        ], $facet->counts());
    }
    public function testInstantiationFromInterval()
    {
        $field = 'price';
        $counts = [
            '[0.0000,10.0000)' => 0,
            '[10.0000,20.0000)' => 4,
            '[20.0000,*)' => 5,
        ];
        $facet = ApacheSolrFacet::fromInterval($field, $counts);
        $this->assertEquals('price', $facet->name());
        $this->assertEquals([
            new FacetCount('[0.0000,10.0000)', 0),
            new FacetCount('[10.0000,20.0000)', 4),
            new FacetCount('[20.0000,*)', 5),
        ], $facet->counts());
    }
}
