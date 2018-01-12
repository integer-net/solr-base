<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */
namespace IntegerNet\Solr\Query;
use IntegerNet\Solr\Config\FuzzyConfig;
use IntegerNet\Solr\Config\ResultsConfig;
use IntegerNet\Solr\Indexer\IndexField;
use IntegerNet\Solr\Query\Params\FilterQueryBuilder;
use IntegerNet\Solr\Implementor\AttributeRepository;
use IntegerNet\Solr\Implementor\Pagination;
use IntegerNet\Solr\Request\HasFilter;
use IntegerNet\Solr\Request\HasPagination;
use IntegerNet\Solr\Implementor\EventDispatcher;

abstract class AbstractParamsBuilder implements ParamsBuilder, HasFilter, HasPagination
{
    /**
     * @var $attributeRepository AttributeRepository
     */
    protected $attributeRespository;

    /**
     * @var $filterQueryBuilder FilterQueryBuilder
     */
    protected $filterQueryBuilder;
    /**
     * @var $pagination Pagination
     */
    protected $pagination;
    /**
     * @var $resultsConfig ResultsConfig
     */
    protected $resultsConfig;
    /**
     * @var $fuzzyConfig FuzzyConfig
     */
    protected $fuzzyConfig;
    /**
     * @var $storeId int
     */
    private $storeId;
    /**
     * @var $eventDispatcher EventDispatcher
     */
    protected $eventDispatcher;
    /**
     * @var bool
     */
    private $broaden = false;

    public function __construct(AttributeRepository $attributeRepository, FilterQueryBuilder $filterQueryBuilder,
                                Pagination $pagination, ResultsConfig $resultsConfig, FuzzyConfig $fuzzyConfig, $storeId, $eventDispatcher)
    {
        $this->attributeRespository = $attributeRepository;
        $this->filterQueryBuilder = $filterQueryBuilder;
        $this->pagination = $pagination;
        $this->resultsConfig = $resultsConfig;
        $this->fuzzyConfig = $fuzzyConfig;
        $this->storeId = (int) $storeId;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param boolean $broaden
     * @return AbstractParamsBuilder
     */
    public function setBroaden($broaden)
    {
        $this->broaden = $broaden;
        return $this;
    }

    /**
     * Return filter query builder used to build the filter query paramter
     *
     * @return FilterQueryBuilder
     */
    public function getFilterQueryBuilder()
    {
        return $this->filterQueryBuilder;
    }

    /**
     * @param string $attributeToReset
     * @return array
     */
    public function buildAsArray($attributeToReset = '')
    {
        if ($attributeToReset) {
            switch($attributeToReset) {
                case 'category';
                    break;
                case 'price':
                    $attributeToReset .= '_f';
                    break;
                default:
                    $attribute = $this->attributeRespository->getAttributeByCode($attributeToReset, $this->storeId);
                    if ($attribute->getBackendType() == 'decimal') {
                        $attributeToReset .= '_f_mv';
                    } else {
                        $attributeToReset .= '_facet';
                    }
            }
        }
        $params = array(
            'q.op' => $this->resultsConfig->getSearchOperator(),
            'fq' => $this->getFilterQuery($attributeToReset),
            'fl' => 'result_html_autosuggest_nonindex,score,sku_s,name_s,product_id',
            'sort' => $this->getSortParam(),
            'facet' => 'true',
            'facet.sort' => 'true',
            'facet.mincount' => '1',
            'facet.field' => $this->getFacetFieldCodes(),
            'defType' => 'edismax',
            'mm' => '1',
        );

        $params = $this->addFacetParams($params);

        if (!$this->fuzzyConfig->isActive() || $this->broaden) {
            $params['mm'] = '0%';
        }
        return $params;
    }

    /**
     * Return current page from pagination
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->pagination->getCurrentPage();
    }

    /**
     * Return page size from pagination
     *
     * @return int
     */
    public function getPageSize()
    {
        return $this->pagination->getPageSize();
    }

    /**
     * Return store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @return array
     */
    abstract protected function getFacetFieldCodes();

    /**
     * @param string $attributeToReset
     * @return string
     */
    protected function getFilterQuery($attributeToReset = '')
    {
        $filterQuery = $this->filterQueryBuilder
            ->setShowOutOfStockProducts($this->resultsConfig->isShowOutOfStock())
            ->buildFilterQuery($this->getStoreId(), $attributeToReset);

        return $filterQuery;
    }

    /**
     * @return string
     */
    private function getSortParam()
    {
        return $this->getCurrentSortField() . ' ' . $this->getCurrentSortDirection();
    }

    /**
     * @return string
     */
    protected function getCurrentSortDirection()
    {
        $direction = $this->pagination->getCurrentDirection();

        if ($this->getCurrentSortField() == 'score') {
            switch (strtolower($direction)) {
                case 'desc':
                    return 'asc';
                default:
                    return 'desc';
            }
        }
        return strtolower($direction);
    }

    /**
     * @return string
     */
    protected function getCurrentSortField()
    {
        $sortField = $this->pagination->getCurrentOrder();
        switch ($sortField) {
            case 'position':
                $sortFieldForSolr = 'score';
                break;
            case 'price':
                $sortFieldForSolr = 'price_f';
                break;
            default:
                $attribute = $this->attributeRespository->getAttributeByCode($sortField, $this->storeId);
                $indexField = new IndexField($attribute, $this->eventDispatcher, true);
                $sortFieldForSolr = $indexField->getFieldName();
        }
        return $sortFieldForSolr;
    }

    /**
     * @param mixed[] $params
     * @return mixed[]
     */
    protected function addFacetParams($params)
    {
        $resultsConfig = $this->resultsConfig;

        $params['fl'] = 'result_html_list_nonindex,result_html_grid_nonindex,score,sku_s,name_s,product_id';
        $params['facet.interval'] = [];
        $params['stats'] = 'true';
        $params['stats.field'] = [];

        foreach ($this->attributeRespository->getFilterableInSearchAttributes($this->storeId) as $filterableAttribute) {
            if ($filterableAttribute->getBackendType() == 'decimal') {
                $indexField = new IndexField($filterableAttribute, $this->eventDispatcher);
                $params['stats.field'][] = $indexField->getFieldName();
                $params['facet.interval'][] = $indexField->getFieldName();
                $params['f.' . $indexField->getFieldName() . '.facet.interval.set'] = ['(*,*)'];
            }
        }

        if (($priceStepsize = $resultsConfig->getPriceStepSize())
            && ($maxPrice = $resultsConfig->getMaxPrice())
        ) {
            $params['facet.range'] = 'price_f';
            $params['f.price_f.facet.range.start'] = 0;
            $params['f.price_f.facet.range.end'] = $maxPrice;
            $params['f.price_f.facet.range.gap'] = $priceStepsize;
        }

        if ($resultsConfig->isUseCustomPriceIntervals()
            && ($customPriceIntervals = $resultsConfig->getCustomPriceIntervals())
        ) {
            $params['f.price_f.facet.interval.set'] = array();
            $lowerBorder = 0;
            foreach ($customPriceIntervals as $upperBorder) {
                $params['f.price_f.facet.interval.set'][] = sprintf('(%f,%f]', $lowerBorder, $upperBorder);
                $lowerBorder = $upperBorder;
            }
            $params['f.price_f.facet.interval.set'][] = sprintf('(%f,%s]', $lowerBorder, '*');
            return $params;
        } else if (($priceStepsize = $resultsConfig->getPriceStepSize())
            && ($maxPrice = $resultsConfig->getMaxPrice())
        ) {
            $params['f.price_f.facet.interval.set'] = array();
            $lowerBorder = 0;
            for ($upperBorder = $priceStepsize; $upperBorder <= $maxPrice; $upperBorder += $priceStepsize) {
                $params['f.price_f.facet.interval.set'][] = sprintf('(%f,%f]', $lowerBorder, $upperBorder);
                $lowerBorder = $upperBorder;
            }
            $params['f.price_f.facet.interval.set'][] = sprintf('(%f,%s]', $lowerBorder, '*');
        }
        return $params;
    }

}