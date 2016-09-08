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


final class ApacheSolrFacet implements Facet
{
    private $name;
    private $counts;

    /**
     * @param string $name
     * @param FacetCount[] $counts
     */
    public function __construct($name, $counts)
    {
        $this->name = $name;
        $this->counts = $counts;
    }

    /**
     * Creates Facet instance from a key,value pair of
     *
     *    response->facet_counts->facet_fields
     *
     * "_facet" suffix from field names will be removed
     *
     * @param string $field
     * @param int[] $counts
     * @return ApacheSolrFacet
     */
    public static function fromField($field, $counts)
    {
        return new self(
            \preg_replace('{_facet$}', '', $field),
            self::facetCountsFromArray((array) $counts)
        );
    }

    /**
     * Creates Facet instance from a key,value pair of
     *
     *    response->facet_counts->facet_interval
     *
     * @param string $field
     * @param int[] $counts
     * @return ApacheSolrFacet
     */
    public static function fromInterval($field, $counts)
    {
        return new self(
            $field,
            self::facetCountsFromArray((array) $counts)
        );
    }

    /**
     * @param array $counts
     * @return array
     */
    private static function facetCountsFromArray($counts)
    {
        return \array_map(function ($value, $count) {
            return new FacetCount($value, $count);
        }, \array_keys($counts), \array_values($counts));
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return FacetCount[]
     */
    public function counts()
    {
        return $this->counts;
    }

}