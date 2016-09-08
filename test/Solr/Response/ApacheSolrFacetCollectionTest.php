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


class ApacheSolrFacetCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApacheSolrFacetCollection
     */
    private $facetCollection;

    protected function setUp()
    {
        $facetCountsObject = $this->objectFromArray([
            'facet_intervals' => [
                'price_f' => ['[0,10)' => 0, '[10,*)' => 1]
            ],
            'facet_something' => [],
            'facet_fields' => [
                'manufacturer_facet' => ['acme' => 1, 'ecma' => 1],
                'color_facet' => ['red' => 1, 'blue' => 1]
            ]
        ]);
        $this->facetCollection = ApacheSolrFacetCollection::fromApacheSolrFacetCounts($facetCountsObject);
    }

    public function testFacetByName()
    {
        $this->assertInstanceOf(Facet::class, $this->facetCollection->facetByName('color'));
        $this->assertEquals('color', $this->facetCollection->facetByName('color')->name());
        $this->assertEquals('price', $this->facetCollection->facetByName('price')->name());
    }

    public function testIterator()
    {
        $this->assertCount(3, $this->facetCollection);
        foreach ($this->facetCollection as $facet) {
            $this->assertInstanceOf(ApacheSolrFacet::class, $facet);
        }
    }

    public function testExcludeFields()
    {
        $filteredCollection = $this->facetCollection->exclude(['price', 'color']);
        $this->assertInstanceOf(ApacheSolrFacetCollection::class, $filteredCollection);
        $this->assertNotSame($this->facetCollection, $filteredCollection);
        $this->assertCount(1, $filteredCollection);
        $this->assertEquals('manufacturer', $filteredCollection->current()->name());
    }

    /**
     * @param $array
     * @return mixed
     */
    private function objectFromArray($array)
    {
        return \json_decode(\json_encode($array));
    }
}
