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
        foreach ($facetCounts->facet_intervals as $identifier => $intervalFacets) {
            if (substr($identifier, -2) == '_f') {
                $attributeCode = substr($identifier, 0, -2);
            } elseif (substr($identifier, -5) == '_f_mv') {
                $attributeCode = substr($identifier, 0, -5);
            } else {
                continue;
            }
            $facets[$attributeCode] = ApacheSolrFacet::fromInterval($attributeCode, $intervalFacets);
        }
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