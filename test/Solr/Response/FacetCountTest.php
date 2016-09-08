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


class FacetCountTest extends \PHPUnit_Framework_TestCase
{
    public function testToArray()
    {
        $facetCount = new FacetCount('field value', 2);
        $this->assertEquals([
            'value' => 'field value',
            'count' => 2
        ], $facetCount->toArray());
    }
    public function testToArrayWithCustomKeys()
    {
        $facetCount = new FacetCount('field value', 2);
        $this->assertEquals([
            'v' => 'field value',
            'c' => 2
        ], $facetCount->toArray('v', 'c'));
    }
    public function testModifiedValue()
    {
        $facetCount = new FacetCount('field value', 1);
        $this->assertEquals(
            new FacetCount('FIELD VALUE', 1),
            $facetCount->withModifiedValue(function($v) { return \strtoupper($v); })
        );
    }
}
