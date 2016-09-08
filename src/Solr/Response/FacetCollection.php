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

use Iterator;

interface FacetCollection extends Iterator
{
    /**
     * @return Facet
     */
    public function current();

    /**
     * @param string $name
     * @return Facet
     */
    public function facetByName($name);

    /**
     * Returns new collection without the given field names
     *
     * @param string[] $names
     * @return ApacheSolrFacetCollection
     */
    public function exclude(array $names);
}