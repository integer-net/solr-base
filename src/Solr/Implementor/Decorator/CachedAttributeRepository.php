<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */

namespace IntegerNet\Solr\Implementor\Decorator;

use IntegerNet\Solr\Exception;
use IntegerNet\Solr\Implementor\Attribute;
use IntegerNet\Solr\Implementor\AttributeRepository;

/**
 * Decorator to cache (memoize) results of method calls. To be used by indexer to prevent repeated
 * database queries
 */
final class CachedAttributeRepository implements AttributeRepository
{
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;
    /**
     * @var array [method][parameters]
     */
    private $memoizedResults = [];

    /**
     * @param AttributeRepository $attributeRepository
     */
    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    private function memoizedCall($methodName, $args)
    {
        $serializedArgs = \serialize($args);
        if (! isset($this->memoizedResults[$methodName][$serializedArgs])) {
            // PHP 5.6+ $this->memoizedResults[$methodName][$serializedArgs] = $this->attributeRepository->$methodName(...$args);
            $this->memoizedResults[$methodName][$serializedArgs] = \call_user_func_array([$this->attributeRepository, $methodName], $args);
        }
        return $this->memoizedResults[$methodName][$serializedArgs];
    }

    /**
     * @param int $storeId
     * @return Attribute[]
     */
    public function getSearchableAttributes($storeId)
    {
        return $this->memoizedCall(__FUNCTION__, func_get_args());
    }

    /**
     * @param int $storeId
     * @return Attribute[]
     */
    public function getSortableAttributes($storeId)
    {
        return $this->memoizedCall(__FUNCTION__, func_get_args());
    }

    /**
     * @param int $storeId
     * @param bool $useAlphabeticalSearch
     * @return Attribute[]
     */
    public function getFilterableInSearchAttributes($storeId, $useAlphabeticalSearch = true)
    {
        return $this->memoizedCall(__FUNCTION__, func_get_args());
    }

    /**
     * @param int $storeId
     * @param bool $useAlphabeticalSearch
     * @return Attribute[]
     */
    public function getFilterableInCatalogAttributes($storeId, $useAlphabeticalSearch = true)
    {
        return $this->memoizedCall(__FUNCTION__, func_get_args());
    }

    /**
     * @param int $storeId
     * @param bool $useAlphabeticalSearch
     * @return Attribute[]
     */
    public function getFilterableInCatalogOrSearchAttributes($storeId, $useAlphabeticalSearch = true)
    {
        return $this->memoizedCall(__FUNCTION__, func_get_args());
    }

    /**
     * @return string[]
     */
    public function getAttributeCodesToIndex()
    {
        return $this->memoizedCall(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $attributeCode
     * @param int $storeId
     * @return Attribute
     * @throws Exception
     */
    public function getAttributeByCode($attributeCode, $storeId)
    {
        return $this->memoizedCall(__FUNCTION__, func_get_args());
    }

}