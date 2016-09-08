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


final class ApacheSolrFacetCollection extends \ArrayIterator implements FacetCollection
{
    /**
     * @param \stdClass $facetCounts
     * @return ApacheSolrFacetCollection
     */
    public static function fromApacheSolrFacetCounts(\stdClass $facetCounts)
    {
        $facets = [];
        foreach ($facetCounts->facet_fields as $field => $counts) {
            $facet = ApacheSolrFacet::fromField($field, $counts);
            $facets[$facet->name()] = $facet;
        }
        $facets['price'] = ApacheSolrFacet::fromInterval('price', $facetCounts->facet_intervals->price_f);
        return new static($facets);
    }
    /**
     * @return Facet
     */
    public function current()
    {
        return parent::current();
    }

    /**
     * @param string $name
     * @return Facet
     */
    public function facetByName($name)
    {
        return $this[$name];
    }

    /**
     * Returns new collection without the given field names
     *
     * @param string[] $names
     * @return ApacheSolrFacetCollection
     */
    public function exclude(array $names)
    {
        $filtered = clone $this;
        foreach ($names as $name) {
            unset($filtered[$name]);
        }
        return $filtered;
    }

}