<?php
namespace IntegerNet\Solr\Config;
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */
final class AutosuggestConfig
{
    const USE_MAGENTO_CONTROLLER = 0;
    const USE_PHP_FILE_WITHOUT_MAGENTO = 1;
    const USE_PHP_FILE_WITH_MAGENTO = 2;

    const CATEGORY_LINK_TYPE_FILTER = 'filter';
    const CATEGORY_LINK_TYPE_DIRECT = 'direct';
    /**
     * @var bool
     */
    private $active;
    /**
     * @var int
     */
    private $usePhpFile;
    /**
     * @var int
     */
    private $maxNumberSearchwordSuggestions;
    /**
     * @var int
     */
    private $maxNumberProductSuggestions;
    /**
     * @var int
     */
    private $maxNumberCategorySuggestions;
    /**
     * @var int
     */
    private $maxNumberCmsPageSuggestions;
    /**
     * @var bool
     */
    private $showCompleteCategoryPath;
    /**
     * @var string
     */
    private $categoryLinkType;
    /**
     * @var mixed[][]
     */
    private $attributeFilterSuggestions;
    /**
     * @var bool
     */
    private $showOutOfStock;
    /**
     * @var bool
     */
    private $fuzzyActiveForCategories;
    /**
     * @var float
     */
    private $fuzzySensitivityForCategories;

    /**
     * @param bool $active
     * @param int $usePhpFile
     * @param int $maxNumberSearchwordSuggestions
     * @param int $maxNumberProductSuggestions
     * @param int $maxNumberCategorySuggestions
     * @param int $maxNumberCmsPageSuggestions
     * @param bool $showCompleteCategoryPath
     * @param string $categoryLinkType
     * @param $attributeFilterSuggestions
     * @param bool $showOutOfStock
     * @param bool $fuzzyActiveForCategories
     * @param float $fuzzySensitivityForCategories
     */
    public function __construct(
        $active,
        $usePhpFile,
        $maxNumberSearchwordSuggestions,
        $maxNumberProductSuggestions,
        $maxNumberCategorySuggestions,
        $maxNumberCmsPageSuggestions,
        $showCompleteCategoryPath,
        $categoryLinkType,
        $attributeFilterSuggestions,
        $showOutOfStock,
        $fuzzyActiveForCategories = true,
        $fuzzySensitivityForCategories = 0.8
    ) {
        $this->active = $active;
        $this->usePhpFile = $usePhpFile;
        $this->maxNumberSearchwordSuggestions = (int)$maxNumberSearchwordSuggestions;
        $this->maxNumberProductSuggestions = (int)$maxNumberProductSuggestions;
        $this->maxNumberCategorySuggestions = (int)$maxNumberCategorySuggestions;
        $this->maxNumberCmsPageSuggestions = (int)$maxNumberCmsPageSuggestions;
        $this->showCompleteCategoryPath = $showCompleteCategoryPath;
        $this->categoryLinkType = $categoryLinkType;
        $this->attributeFilterSuggestions = $attributeFilterSuggestions;
        $this->showOutOfStock = $showOutOfStock;
        $this->fuzzyActiveForCategories = $fuzzyActiveForCategories;
        $this->fuzzySensitivityForCategories = $fuzzySensitivityForCategories;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return int
     */
    public function getUsePhpFile()
    {
        return $this->usePhpFile;
    }

    /**
     * @return int
     */
    public function getMaxNumberSearchwordSuggestions()
    {
        return $this->maxNumberSearchwordSuggestions;
    }

    /**
     * @return int
     */
    public function getMaxNumberProductSuggestions()
    {
        return $this->maxNumberProductSuggestions;
    }

    /**
     * @return int
     */
    public function getMaxNumberCategorySuggestions()
    {
        return $this->maxNumberCategorySuggestions;
    }

    /**
     * @return int
     */
    public function getMaxNumberCmsPageSuggestions()
    {
        return $this->maxNumberCmsPageSuggestions;
    }

    /**
     * @return boolean
     */
    public function isShowCompleteCategoryPath()
    {
        return $this->showCompleteCategoryPath;
    }

    /**
     * @return string
     */
    public function getCategoryLinkType()
    {
        return $this->categoryLinkType;
    }

    /**
     * Array of items in the form ["attribute_code" => string, "max_number_suggestions" => int, "sorting" => int]
     *
     * @return mixed[][]
     */
    public function getAttributeFilterSuggestions()
    {
        return $this->attributeFilterSuggestions;
    }

    /**
     * @return bool
     */
    public function isShowOutOfStock()
    {
        return $this->showOutOfStock;
    }

    /**
     * @return bool
     */
    public function isFuzzyActiveForCategories()
    {
        return $this->fuzzyActiveForCategories;
    }

    /**
     * @return float
     */
    public function getFuzzySensitivityForCategories()
    {
        return $this->fuzzySensitivityForCategories;
    }
}