<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */
namespace IntegerNet\Solr\Request;

use IntegerNet\Solr\Config\FuzzyConfig;
use IntegerNet\Solr\Event\Transport;
use IntegerNet\Solr\Implementor\AttributeRepository;
use IntegerNet\Solr\Implementor\EventDispatcher;
use IntegerNet\Solr\Implementor\Pagination;
use Apache_Solr_Response;
use IntegerNet\Solr\Indexer\IndexField;
use IntegerNet\Solr\Query\Params\FilterQueryBuilder;
use IntegerNet\Solr\Query\ParamsBuilder;
use IntegerNet\Solr\Query\SearchParamsBuilder;
use IntegerNet\Solr\Query\SearchQueryBuilder;
use IntegerNet\Solr\Resource\ResourceFacade;
use IntegerNet\Solr\Resource\SolrResponse;
use IntegerNet\Solr\Resource\LoggerDecorator;
use Psr\Log\LoggerInterface;

class SearchRequest implements Request, HasFilter
{
    const PAGESIZE_ALL = 99999;
    /**
     * @var $resource ResourceFacade
     */
    private $resource;
    /**
     * @var $queryBuilder SearchQueryBuilder
     */
    private $queryBuilder;
    /**
     * @var $pagination Pagination
     */
    private $pagination;
    /**
     * @var $fuzzyConfig FuzzyConfig
     */
    private $fuzzyConfig;
    /**
     * @var $paramsBuilder SearchParamsBuilder
     */
    private $paramsBuilder;
    /**
     * @var $eventDispatcher EventDispatcher
     */
    private $eventDispatcher;
    /**
     * @var $logger LoggerDecorator
     */
    private $logger;
    /**
     * Second run to Solr, when the first search hasn't found anything!
     * @var $foundNoResults bool
     */
    private $foundNoResults = false;
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @param ResourceFacade $resource
     * @param SearchQueryBuilder $queryBuilder
     * @param Pagination $pagination
     * @param FuzzyConfig $fuzzyConfig
     * @param EventDispatcher $eventDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceFacade $resource,
        SearchQueryBuilder $queryBuilder,
        Pagination $pagination,
        FuzzyConfig $fuzzyConfig,
        EventDispatcher $eventDispatcher,
        LoggerInterface $logger,
        AttributeRepository $attributeRepository
    ) {
        $this->resource = $resource;
        $this->queryBuilder = $queryBuilder;
        $this->pagination = $pagination;
        $this->fuzzyConfig = $fuzzyConfig;
        $this->paramsBuilder = $queryBuilder->getParamsBuilder();
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = new LoggerDecorator($logger);
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @return SearchParamsBuilder
     */
    private function getParamsBuilder()
    {
        return $this->paramsBuilder;
    }

    /**
     * @return FilterQueryBuilder
     */
    public function getFilterQueryBuilder()
    {
        return $this->getParamsBuilder()->getFilterQueryBuilder();
    }

    /**
     * @param string[] $activeFilterAttributeCodes
     * @return SolrResponse
     */
    public function doRequest($activeFilterAttributeCodes = array())
    {
        $pageSize = $this->getParamsBuilder()->getPageSize() * $this->getParamsBuilder()->getCurrentPage();
        $isFuzzyActive = $this->fuzzyConfig->isActive();
        $minimumResults = $this->fuzzyConfig->getMinimumResults();
        if ($this->getCurrentSort() !== 'position') {
            $result = $this->getResultFromRequest($pageSize, $isFuzzyActive, $activeFilterAttributeCodes);
            if ($result->documents()->count() === 0) {
                $result = $this->getBroaderResult($activeFilterAttributeCodes, $pageSize, $result);
            }
            return $this->sliceResult($result);
        } else {
            $result = $this->getResultFromRequest($isFuzzyActive ? self::PAGESIZE_ALL : $pageSize, false, $activeFilterAttributeCodes);
            $numberResults = $result->documents()->count() ;
            if ($isFuzzyActive && (($minimumResults == 0) || ($numberResults < $minimumResults))) {

                $fuzzyResult = $this->getResultFromRequest(self::PAGESIZE_ALL, true, $activeFilterAttributeCodes);
                $result = $result->merge($fuzzyResult, $pageSize);
            }
            if ($result->documents()->count() === 0) {
                $result = $this->getBroaderResult($activeFilterAttributeCodes, $pageSize, $result);
            }
            $result->updateDecimalFacetData();
            return $this->sliceResult($result);
        }
    }

    /**
     * Remove all but last page from multipage result
     *
     * @param SolrResponse $result
     * @return SolrResponse
     */
    private function sliceResult(SolrResponse $result)
    {
        $pageSize = $this->getParamsBuilder()->getPageSize();
        $firstItemNumber = ($this->getParamsBuilder()->getCurrentPage() - 1) * $pageSize;
        $result->slice($firstItemNumber, $pageSize);
        return $result;
    }
    /**
     * @return string
     */
    private function getCurrentSort()
    {
        return $this->pagination->getCurrentOrder();
    }


    /**
     * @param int $pageSize
     * @param boolean $fuzzy
     * @param string[] $activeFilterAttributeCodes
     * @return SolrResponse
     */
    private function getResultFromRequest($pageSize, $fuzzy = true, $activeFilterAttributeCodes = array())
    {
        $query = $this->queryBuilder
            ->setAllowFuzzy($fuzzy)
            ->setBroaden($this->foundNoResults)
            ->setAttributeToReset('')
            ->build();
        $transportObject = new Transport(array(
            'store_id' => $this->getParamsBuilder()->getStoreId(),
            'query_text' => $query->getQueryText(),
            'start_item' => 0,
            'page_size' => $pageSize,
            'params' => $query->getParams(),
        ));

        $this->eventDispatcher->dispatch('integernet_solr_before_search_request', array('transport' => $transportObject));

        $startTime = microtime(true);

        $result = $this->getResource()->search(
            $transportObject->getStoreId(),
            $transportObject->getQueryText(),
            $transportObject->getStartItem(), // Start item
            $transportObject->getPageSize(), // Items per page
            $transportObject->getParams()
        );

        $this->logger->logResult($result, microtime(true) - $startTime);

        $this->logger->debug((($fuzzy) ? 'Fuzzy Search' : 'Normal Search'));
        $this->logger->debug('Query over all searchable fields: ' . $transportObject['query_text']);
        $this->logger->debug('Filter Query: ' . $transportObject['params']['fq']);

        foreach ($activeFilterAttributeCodes as $attributeCode) {

            $query = $this->queryBuilder
                ->setAllowFuzzy($fuzzy)
                ->setBroaden($this->foundNoResults)
                ->setAttributeToReset($attributeCode)
                ->build();

            $transportObject = new Transport(array(
                'store_id' => $this->getParamsBuilder()->getStoreId(),
                'query_text' => $query->getQueryText(),
                'start_item' => 0,
                'page_size' => 0,
                'params' => $query->getParams(),
            ));

            $this->eventDispatcher->dispatch('integernet_solr_before_search_request', array('transport' => $transportObject));

            $parentResult = $this->getResource()->search(
                $transportObject->getStoreId(),
                $transportObject->getQueryText(),
                $transportObject->getStartItem(), // Start item
                $transportObject->getPageSize(), // Items per page
                $transportObject->getParams()
            );

            switch ($attributeCode) {
                case 'category':
                    $facetCode = $attributeCode;
                    break;
                default:
                    $facetCode = $attributeCode . '_facet';
            }
            if (isset($parentResult->facet_counts->facet_fields->{$facetCode})) {
                $result->facet_counts->facet_fields->{$facetCode} = $parentResult->facet_counts->facet_fields->{$facetCode};
            }
            if ($attributeCode == 'price' && isset($parentResult->facet_counts->facet_ranges->price_f)) {
                $result->facet_counts->facet_ranges->price_f = $parentResult->facet_counts->facet_ranges->price_f;
            }
            if ($attributeCode == 'price' && isset($parentResult->facet_counts->facet_intervals->price_f)) {
                $result->facet_counts->facet_intervals->price_f = $parentResult->facet_counts->facet_intervals->price_f;
            }
            if ($attributeCode == 'category') {
                continue;
            }
            $attribute = $this->attributeRepository->getAttributeByCode($attributeCode, null);
            if ($attribute->getBackendType() == 'decimal') {
                $indexField = new IndexField($attribute, $this->eventDispatcher);
                $fieldName = $indexField->getFieldName();
                if (isset($parentResult->facet_counts->facet_intervals->$fieldName)) {
                    $result->facet_counts->facet_intervals->$fieldName = $parentResult->facet_counts->facet_intervals->$fieldName;
                }
                if (isset($parentResult->stats->stats_fields->$fieldName)) {
                    $result->stats->stats_fields->$fieldName = $parentResult->stats->stats_fields->$fieldName;
                }
            }
        }
        $this->eventDispatcher->dispatch('integernet_solr_after_search_request', array('result' => $result));
        return $result;
    }

    /**
     * @return ResourceFacade
     */
    private function getResource()
    {
        return $this->resource;
    }

    /**
     * @param string[] $activeFilterAttributeCodes
     * @param int $pageSize
     * @param SolrResponse
     * @return SolrResponse
     */
    private function getBroaderResult($activeFilterAttributeCodes, $pageSize, $result)
    {
        $this->foundNoResults = true;
        $check = explode(' ', $this->queryBuilder->getSearchString()->getRawString());
        if (count($check) > 1) {
            $result = $this->getResultFromRequest($pageSize, false, $activeFilterAttributeCodes);
        }
        $this->foundNoResults = false;
        return $result;
    }
}