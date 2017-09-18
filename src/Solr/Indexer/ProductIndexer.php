<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */
namespace IntegerNet\Solr\Indexer;
use IntegerNet\Solr\Implementor\Decorator\CachedAttributeRepository;
use IntegerNet\Solr\Implementor\PagedProductIterator;
use IntegerNet\Solr\Implementor\ProductRenderer;
use IntegerNet\Solr\Implementor\StoreEmulation;
use IntegerNet\Solr\Indexer\Data\ProductAssociation;
use IntegerNet\Solr\Indexer\Data\ProductIdChunks;
use IntegerNet\Solr\Resource\ResourceFacade;
use IntegerNet\Solr\Implementor\Config;
use IntegerNet\Solr\Implementor\EventDispatcher;
use IntegerNet\Solr\Implementor\AttributeRepository;
use IntegerNet\Solr\Implementor\Attribute;
use IntegerNet\Solr\Implementor\Product;
use IntegerNet\Solr\Implementor\ProductIterator;
use IntegerNet\Solr\Implementor\ProductRepository;
use IntegerNet\Solr\Implementor\IndexCategoryRepository;

class ProductIndexer
{
    const CONTENT_TYPE = 'product';

    /** @var  int */
    private $_defaultStoreId;
    /**
     * Configuration reader, by store id
     *
     * @var  Config[]
     */
    private $_config;
    /** @var  ResourceFacade */
    private $_resource;
    /** @var  EventDispatcher */
    private $_eventDispatcher;
    /** @var  AttributeRepository */
    private $_attributeRepository;
    /** @var  IndexCategoryRepository */
    private $_categoryRepository;
    /** @var  ProductRepository */
    private $productRepository;
    /** @var  ProductRenderer */
    private $_renderer;
    /** @var StoreEmulation */
    private $storeEmulation;

    /**
     * @param int $defaultStoreId
     * @param Config[] $_config
     * @param ResourceFacade $_resource
     * @param EventDispatcher $_eventDispatcher
     * @param AttributeRepository $_attributeRepository
     * @param IndexCategoryRepository $_categoryRepository
     * @param ProductRepository $_productRepository
     * @param ProductRenderer $_renderer
     * @param StoreEmulation $storeEmulation
     */
    public function __construct($defaultStoreId, array $_config, ResourceFacade $_resource, EventDispatcher $_eventDispatcher,
                                AttributeRepository $_attributeRepository, IndexCategoryRepository $_categoryRepository,
                                ProductRepository $_productRepository, ProductRenderer $_renderer, StoreEmulation $storeEmulation)
    {
        $this->_defaultStoreId = $defaultStoreId;
        $this->_config = $_config;
        $this->_resource = $_resource;
        $this->_eventDispatcher = $_eventDispatcher;
        $this->_attributeRepository = new CachedAttributeRepository($_attributeRepository);
        $this->_categoryRepository = $_categoryRepository;
        $this->productRepository = $_productRepository;
        $this->_renderer = $_renderer;
        $this->storeEmulation = $storeEmulation;
    }

    protected function _getStoreConfig($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->_defaultStoreId;
        }
        $storeId = (int)$storeId;
        if (!isset($this->_config[$storeId])) {
            throw new \Exception("Store with ID {$storeId} not found.");
        }
        return $this->_config[$storeId];
    }

    /**
     * @param array|null $productIds Restrict to given Products if this is set
     * @param boolean|string $emptyIndex Whether to truncate the index before refilling it
     * @param null|int[] $restrictToStoreIds
     * @param null|int $sliceId
     * @param null|int $totalNumberSlices
     * @throws \Exception
     * @throws \IntegerNet\Solr\Exception
     */
    public function reindex($productIds = null, $emptyIndex = false, $restrictToStoreIds = null, $sliceId = null, $totalNumberSlices = null)
    {
        if (is_null($productIds) && is_null($sliceId)) {
            $this->checkSwapCoresConfiguration($restrictToStoreIds);
        }

        foreach($this->_config as $storeId => $storeConfig) {
            if (!is_null($restrictToStoreIds) && !in_array($storeId, $restrictToStoreIds)) {
                continue;
            }

            if (!$storeConfig->getGeneralConfig()->isActive()) {
                continue;
            }

            $this->storeEmulation->start($storeId);
            try {

                if (is_null($productIds) && is_null($sliceId) && $storeConfig->getIndexingConfig()->isSwapCores()) {
                    $this->activateSwapCore();
                }

                if (
                    ($emptyIndex && is_null($sliceId) && $storeConfig->getIndexingConfig()->isDeleteDocumentsBeforeIndexing())
                    || $emptyIndex === 'force'
                ) {
                    $this->clearIndex($storeId);
                }

                $pageSize = intval($storeConfig->getIndexingConfig()->getPagesize());
                if ($pageSize <= 0) {
                    $pageSize = 100;
                }

                if ($productIds == null) {
                    $productIdsToIndex = $this->productRepository->getAllProductIds($sliceId, $totalNumberSlices);
                } else {
                    $productIdsToIndex = $productIds;
                }

                $associations = $this->productRepository->getProductAssociations($productIdsToIndex);
                $chunks = ProductIdChunks::withAssociationsTogether(
                    $productIdsToIndex,
                    $associations,
                    $pageSize);
                $productIterator = $this->productRepository->getProductsInChunks($storeId, $chunks);
                $this->_indexProductCollection($emptyIndex, $productIterator, $storeId, $productIdsToIndex, $associations);

                if (is_null($productIds) && is_null($sliceId) && $storeConfig->getIndexingConfig()->isSwapCores()) {
                    $this->deactivateSwapCore();
                }
            } catch (\Exception $e) {
                $this->storeEmulation->stop();
                throw $e;
            }
            $this->storeEmulation->stop();
        }

        if (is_null($productIds) && is_null($sliceId)) {
            $this->swapCores($restrictToStoreIds);
        }
    }

    /**
     * @param string[] $productIds
     */
    public function deleteIndex($productIds)
    {
        foreach($this->_config as $storeId => $storeConfig) {

            if (! $storeConfig->getGeneralConfig()->isActive()) {
                continue;
            }

            $ids = array();

            foreach($productIds as $productId) {
                $ids[] = $productId . '_' . $storeId;
            }

            $this->_getResource()->deleteByMultipleIds($storeId, $ids);
        }
    }


    /**
     * Generate single product data for Solr
     *
     * @param Product $product
     * @param ProductIterator $children
     * @return IndexDocument
     */
    protected function _getProductData(Product $product, ProductIterator $children)
    {
        $categoryIds = $this->_categoryRepository->getCategoryIds($product);
        $productData = new IndexDocument(array(
            'id' => $product->getSolrId(), // primary identifier, must be unique
            'product_id' => $product->getId(),
            'category' => $categoryIds, // @todo get category ids from parent anchor categories as well
            'category_name_t_mv' => $this->_categoryRepository->getCategoryNames($categoryIds, $product->getStoreId()),
            'store_id' => $product->getStoreId(),
            'content_type' => self::CONTENT_TYPE,
            'is_visible_in_catalog_i' => intval($product->isVisibleInCatalog()),
            'is_visible_in_search_i' => intval($product->isVisibleInSearch()),
            'has_special_price_i' => intval($product->hasSpecialPrice()),
            'is_in_stock_i' => intval($product->isInStock()),
        ));

        $this->_addBoostToProductData($product, $productData);

        $this->_addFacetsToProductData($product, $productData, $children);

        $this->_addSearchDataToProductData($product, $productData, $children);

        $this->_addSortingDataToProductData($product, $productData);

        $this->_addResultHtmlToProductData($product, $productData);

        $this->_addCategoryProductPositionsToProductData($product, $productData);

        $this->_eventDispatcher->dispatch('integernet_solr_get_product_data', array(
            'product' => $product,
            'product_data' => $productData,
            'children' => $children,
        ));

        return $productData;
    }

    /**
     * Get unique identifier for Solr
     *
     * @param \IntegerNet\Solr\Implementor\Product $product
     * @return string
     */
    protected function _getSolrId($product)
    {
        return $this->_getSolrIdByProductIdAndStoreId($product->getId(), $product->getStoreId());
    }

    /**
     * Get unique identifier for Solr
     *
     * @param int $productId
     * @param int $storeId
     * @return string
     */
    protected function _getSolrIdByProductIdAndStoreId($productId, $storeId)
    {
        return $productId . '_' . $storeId;
    }

    /**
     * @param Product $product
     * @param IndexDocument $productData
     * @param ProductIterator $children
     */
    protected function _addFacetsToProductData(Product $product, IndexDocument $productData, ProductIterator $children)
    {
        foreach ($this->_attributeRepository->getFilterableInCatalogOrSearchAttributes($product->getStoreId()) as $attribute) {

            if ($attribute->getAttributeCode() == 'price') {
                $price = $product->getPrice();
                $productData->setData('price_f', floatval($price));
                continue;
            }

            $facetFieldName = $attribute->getAttributeCode() . '_facet';
            if ($product->getAttributeValue($attribute) !== null) {

                switch ($attribute->getFacetType()) {
                    case Attribute::FACET_TYPE_SELECT:
                        $rawValue = $product->getAttributeValue($attribute);
                        if ($rawValue && $this->_isInteger($rawValue)) {
                            $productData->setData($facetFieldName, $rawValue);
                        }
                        break;
                    case Attribute::FACET_TYPE_MULTISELECT:
                        $rawValue = $product->getAttributeValue($attribute);
                        if ($rawValue && $this->_isInteger($rawValue)) {
                            $productData->setData($facetFieldName, explode(',', $rawValue));
                        }
                        break;
                }

                $indexField = new IndexField($attribute, $this->_eventDispatcher);
                $fieldName = $indexField->getFieldName();
                if (!$productData->hasData($fieldName)) {
                    $value = $product->getSearchableAttributeValue($attribute);
                    if (!empty($value)) {
                        $productData->setData($fieldName, $value);

                        if (strstr($fieldName, '_t') == true && $attribute->getUsedForSortBy()) {
                            $productData->setData(
                                $indexField->forSorting()->getFieldName(),
                                $value
                            );
                        }
                    }
                }
            }

            if ($attribute->getBackendType() != Attribute::BACKEND_TYPE_DECIMAL) {

                foreach($children as $childProduct) {
                    /** @var $childProduct Product */
                    if ($childValues = $childProduct->getAttributeValue($attribute)
                    ) {
                        foreach(array_map('trim', explode(',', $childValues)) as $childValue) {
                            if (!$productData->hasData($facetFieldName)) {
                                $productData->setData($facetFieldName, $childValue);
                            } else {
                                $fieldValue = $productData->getData($facetFieldName);
                                if (!is_array($fieldValue) && $childValue != $fieldValue) {
                                    $productData->setData($facetFieldName, array($fieldValue, $childValue));
                                } else {
                                    if (is_array($fieldValue) && !in_array($childValue, $fieldValue)) {
                                        $fieldValue[] = $childValue;
                                        $productData->setData($facetFieldName, $fieldValue);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * The schema expected for facet attributes integer values
     *
     * @param string $rawValue
     * @return bool
     */
    protected function _isInteger($rawValue)
    {
        $rawValues = explode(',', $rawValue);

        foreach ($rawValues as $value) {
            if (!is_numeric($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Product $product
     * @param IndexDocument $productData
     * @param ProductIterator $children
     */
    protected function _addSearchDataToProductData(Product $product, IndexDocument $productData, ProductIterator $children)
    {

        if (!$productData->getData('price_f')) {
            $productData->setData('price_f', 0.00);
        }

        foreach ($this->_attributeRepository->getSearchableAttributes($product->getStoreId()) as $attribute) {

            if (($attribute->getAttributeCode() == 'price') && ($productData->getData('price_f') > 0)) {
                continue;
            }

            $indexField = new IndexField($attribute, $this->_eventDispatcher);
            $fieldName = $indexField->getFieldName();

            $solrBoost = floatval($attribute->getSolrBoost());
            if ($solrBoost != 1) {
                $productData->setData($fieldName . '_boost', $solrBoost);
            }

            if ($product->getAttributeValue($attribute)
                && $value = $product->getSearchableAttributeValue($attribute)
            ) {
                $productData->setData($fieldName, $value);

                if (strstr($fieldName, '_t') == true && $attribute->getUsedForSortBy()) {
                    $productData->setData(
                        $indexField->forSorting()->getFieldName(),
                        $value
                    );
                }
            }

            if ($attribute->getBackendType() != Attribute::BACKEND_TYPE_DECIMAL) {

                foreach($children as $childProduct) {
                    /** @var $childProduct Product */
                    if ($childProduct->getAttributeValue($attribute)
                        && $childValue = $childProduct->getSearchableAttributeValue($attribute)
                    ) {
                        if (!$productData->hasData($fieldName)) {
                            $productData->setData($fieldName, $childValue);
                        } else {
                            if (!$attribute->getUsedForSortBy()) {
                                $fieldValue = $productData->getData($fieldName);
                                if (!is_array($fieldValue) && $childValue != $fieldValue) {
                                    $productData->setData($fieldName, array($fieldValue, $childValue));
                                } else {
                                    if (!is_array($childValue)) {
                                        $childValue = array($childValue);
                                    }
                                    foreach($childValue as $singleChildValue) {
                                        if (is_array($fieldValue) && !in_array($singleChildValue, $fieldValue)) {
                                            $fieldValue[] = $singleChildValue;
                                            $productData->setData($fieldName, $fieldValue);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!$productData->getData('price_f')) {
            $productData->setData('price_f', 0.00);
        }
    }



    /**
     * @param Product $product
     * @param IndexDocument $productData
     */
    protected function _addSortingDataToProductData(Product $product, IndexDocument $productData)
    {
        foreach ($this->_attributeRepository->getSortableAttributes($product->getStoreId()) as $attribute) {

            $indexField = new IndexField($attribute, $this->_eventDispatcher, true);
            $fieldName = $indexField->getFieldName();

            if (!$productData->getData($fieldName)
                && strlen($product->getAttributeValue($attribute))
                && strlen($value = $product->getSearchableAttributeValue($attribute))
            ) {
                $productData->setData($fieldName, $value);
            }
        }
    }

    /**
     * @param Product $product
     * @param IndexDocument $productData
     */
    protected function _addResultHtmlToProductData(Product $product, IndexDocument $productData)
    {
        $useHtmlForResults = $this->_getStoreConfig($product->getStoreId())->getResultsConfig()->isUseHtmlFromSolr();
        $this->_renderer->addResultHtmlToProductData($product, $productData, $useHtmlForResults);
    }

    /**
     * @param Product $product
     * @param IndexDocument $productData
     */
    protected function _addCategoryProductPositionsToProductData(Product $product, IndexDocument $productData)
    {
        foreach($this->_categoryRepository->getCategoryPositions($product) as $positionRow) {
            $productData['category_' . $positionRow['category_id'] . '_position_i'] = $positionRow['position'];
        }
    }


    /**
     * @param Product $product
     * @param IndexDocument $productData
     */
    protected function _addBoostToProductData(Product $product, IndexDocument $productData)
    {
        if ($boost = $product->getSolrBoost()) {
            if ($boost > 0) {
                $productData->setData('_boost', $boost);
            } else {
                $productData->setData('_boost', 1);
            }
        }
    }

    /**
     * @return ResourceFacade
     */
    protected function _getResource()
    {
        return $this->_resource;
    }

    /**
     * @param boolean $emptyIndex
     * @param PagedProductIterator $productIterator
     * @param int $storeId
     * @param int[] $productIds
     * @param ProductAssociation[] $associations
     * @return int
     */
    protected function _indexProductCollection($emptyIndex, PagedProductIterator $productIterator, $storeId, $productIds, $associations)
    {
        $idsForDeletion = array();
        $documentQueue = new IndexDocumentQueue($this->_resource, $storeId);
        $productIds = array_flip((array)$productIds);

        $productIterator->setPageCallback([$documentQueue, 'flush']);
        foreach ($productIterator as $product) {
            if (isset($productIds[$product->getId()])) {
                unset($productIds[$product->getId()]);
            }
            if ($product->isIndexable()) {
                $childrenIds = isset($associations[$product->getId()]) ? $associations[$product->getId()]->childrenIds() : [];
                $documentQueue->add($this->_getProductData($product, $productIterator->subset($childrenIds)));
            } else {
                $idsForDeletion[] = $this->_getSolrId($product);
            }
        }

        foreach ($productIds as $productId => $value) {
            $idsForDeletion[] = $this->_getSolrIdByProductIdAndStoreId($productId, $storeId);
        }

        if (!$emptyIndex && sizeof($idsForDeletion)) {
            $this->_getResource()->deleteByMultipleIds($storeId, $idsForDeletion);
        }
        return $storeId;
    }

    /**
     * @param $storeId
     */
    public function clearIndex($storeId)
    {
        $this->_getResource()->deleteAllDocuments($storeId, self::CONTENT_TYPE);
    }

    public function activateSwapCore()
    {
        $this->_getResource()->setUseSwapIndex();
    }

    public function deactivateSwapCore()
    {
        $this->_getResource()->setUseSwapIndex(false);
    }

    /**
     * @param null|int[] $restrictToStoreIds
     */
    public function swapCores($restrictToStoreIds)
    {
        $this->_getResource()->swapCores($restrictToStoreIds);
    }

    /**
     * @param $restrictToStoreIds
     */
    public function checkSwapCoresConfiguration($restrictToStoreIds)
    {
        return $this->_getResource()->checkSwapCoresConfiguration($restrictToStoreIds);
    }


}