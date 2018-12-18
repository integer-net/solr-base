<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */

namespace IntegerNet\Solr\Query\Params;

use IntegerNet\Solr\Config\ResultsConfig;
use IntegerNet\Solr\Implementor\Attribute;
use IntegerNet\Solr\Implementor\AttributeRepository;

class FilterQueryBuilder
{
    /**
     * @var $resultsConfig ResultsConfig
     */
    private $resultsConfig;
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;
    /**
     * @var $isCategoryPage bool
     */
    private $isCategoryPage = false;
    /**
     * @var $showOutOfStock bool
     */
    private $showOutOfStockProducts = false;
    /**
     * @var $filters array
     */
    private $filters = array();
    /**
     * @var int
     */
    private $storeId;

    /**
     * @param ResultsConfig $resultsConfig
     */
    public function __construct(
        ResultsConfig $resultsConfig,
        AttributeRepository $attributeRepository,
        $storeId
    ) {
        $this->resultsConfig = $resultsConfig;
        $this->attributeRepository = $attributeRepository;
        $this->storeId = $storeId;
    }

    public static function noFilterQueryBuilder(
        ResultsConfig $resultsConfig,
        AttributeRepository $attributeRepository,
        $storeId
    ) {
        return new self($resultsConfig, $attributeRepository, $storeId);
    }

    /**
     * @return ResultsConfig
     */
    private function getResultsConfig()
    {
        return $this->resultsConfig;
    }

    /**
     * @param bool $isCategoryPage
     * @return $this
     */
    public function setIsCategoryPage($isCategoryPage)
    {
        $this->isCategoryPage = $isCategoryPage;
        return $this;
    }

    /**
     * @param bool $showOutOfStockProducts
     * @return $this
     */
    public function setShowOutOfStockProducts($showOutOfStockProducts)
    {
        $this->showOutOfStockProducts = $showOutOfStockProducts;
        return $this;
    }

    /**
     * @param Attribute $attribute
     * @param $value
     * @return $this
     */
    public function addAttributeFilter(Attribute $attribute, $value)
    {
        if (!$this->isFilterableAttribute($attribute)) {
            return $this;
        }
        if (!$this->isValidAttributeFilterValue($value)) {
            return $this;
        }
        $this->_addFilter($attribute->getAttributeCode() . '_facet', $value);
        return $this;
    }


    /**
     * @param int $categoryId
     * @return $this
     */
    public function addCategoryFilter($categoryId)
    {
        $this->_addFilter('category', $categoryId);
        return $this;
    }

    /**
     * @param int $range
     * @param int $index
     */
    public function addPriceRangeFilterByConfiguration($range, $index)
    {
        if ($this->getResultsConfig()->isUseCustomPriceIntervals()
            && $customPriceIntervals = $this->getResultsConfig()->getCustomPriceIntervals()
        ) {
            $this->addPriceRangeFilterWithCustomIntervals($index, $customPriceIntervals);
        } else {
            $this->addPriceRangeFilter($range, $index);
        }
    }

    /**
     * @param $range
     * @param $index
     * @return $this
     */
    public function addPriceRangeFilter($range, $index)
    {
        $maxPrice = $index * $range;
        $minPrice = $maxPrice - $range;
        $this->_addFilter('price_f', sprintf('[%f TO %f]', $minPrice, $maxPrice));
        return $this;
    }

    /**
     * @param $index
     * @param $customPriceIntervals
     * @return $this
     */
    public function addPriceRangeFilterWithCustomIntervals($index, $customPriceIntervals)
    {
        $lowerBorder = 0;
        $i = 1;
        foreach (explode(',', $customPriceIntervals) as $upperBorder) {
            if ($i == $index) {
                $this->_addFilter('price_f', sprintf('[%f TO %f]', $lowerBorder, $upperBorder));
                return $this;
            }
            $i++;
            $lowerBorder = $upperBorder;
            continue;
        }
        $this->_addFilter('price_f', sprintf('[%f TO %f]', $lowerBorder, '*'));
        return $this;
    }

    /**
     * @param float|float[] $minPrice
     * @param float|float[] $maxPrice
     * @return $this
     * @throws \Exception
     */
    public function addPriceRangeFilterByMinMax($minPrice, $maxPrice = 0.0)
    {
        return $this->addRangeFilterByMinMax('price_f', $minPrice, $maxPrice);
    }

    /**
     * @param string $facetName
     * @param float|float[] $minValue
     * @param float|float[] $maxValue
     * @return $this
     * @throws \Exception
     */
    public function addRangeFilterByMinMax($facetName, $minValue, $maxValue = 0.0)
    {
        if (is_array($minValue) && is_array($maxValue)) {
            if (sizeof($minValue) != sizeof($maxValue)) {
                throw new \Exception('Arrays of min and max values must be of same size.');
            }
            foreach ($minValue as $index => $void) {
                $this->addRangeFilterByMinMax($facetName, $minValue[$index], $maxValue[$index]);
            }
            return $this;
        }
        if (!is_numeric($minValue)) {
            return $this;
        }
        if ($maxValue) {
            $this->_addFilter($facetName, sprintf('[%f TO %f]', $minValue, $maxValue));
        } else {
            $this->_addFilter($facetName, sprintf('[%f TO *]', $minValue));
        }
        return $this;
    }

    /**
     * @param int $storeId
     * @param string $attributeToReset
     * @return string
     */
    public function buildFilterQuery($storeId, $attributeToReset = '')
    {
        $filterQuery = 'content_type:product AND store_id:' . $storeId;
        if ($this->isCategoryPage) {
            $filterQuery .= ' AND is_visible_in_catalog_i:1';
        } else {
            $filterQuery .= ' AND is_visible_in_search_i:1';
        }

        foreach ($this->filters as $attributeCode => $value) {
            if ($attributeCode == $attributeToReset) {
                continue;
            }
            if (is_array($value)) {
                $filterQuery .= ' AND (';
                $filterQueryParts = array();
                foreach ($value as $singleValue) {
                    $filterQueryParts[] = $attributeCode . ':' . $singleValue;
                }
                $filterQuery .= implode(' OR ', $filterQueryParts);
                $filterQuery .= ')';
            } else {
                $filterQuery .= ' AND ' . $attributeCode . ':' . $value;
            }
        }

        if (!$this->showOutOfStockProducts) {
            $filterQuery .= ' AND -is_in_stock_i:0';
        }

        return $filterQuery;
    }

    /**
     * Add new filter without overwriting existing filters
     *
     * @param string $facetName
     * @param string|int $value
     */
    protected function _addFilter($facetName, $value)
    {
        if (isset($this->filters[$facetName])) {
            $currentValue = $this->filters[$facetName];
            if (!is_array($currentValue)) {
                $currentValue = array($currentValue);
            }
            $currentValue[] = $value;
            $value = $currentValue;
        }
        $this->filters[$facetName] = $value;
    }

    /**
     * @param Attribute $attribute
     * @return bool
     */
    private function isFilterableAttribute(Attribute $attribute)
    {
        if ($this->isCategoryPage) {
            $filterableAttributes = $this->attributeRepository->getFilterableInCatalogAttributes($this->storeId);
        } else {
            $filterableAttributes = $this->attributeRepository->getFilterableInSearchAttributes($this->storeId);
        }
        foreach ($filterableAttributes as $filterableAttribute) {
            if ($filterableAttribute->getAttributeCode() == $attribute->getAttributeCode()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Value must be an option ID or an array of option IDs
     *
     * @param mixed $value
     * @return bool
     */
    private function isValidAttributeFilterValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $subValue) {
                if (!$this->isValidAttributeFilterValue($subValue)) {
                    return false;
                }
            }
            return true;
        }

        return ctype_digit($value);
    }
}