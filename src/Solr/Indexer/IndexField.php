<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Solr
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */
namespace IntegerNet\Solr\Indexer;

use IntegerNet\Solr\Implementor\Attribute;
use IntegerNet\Solr\Event\Transport;
use IntegerNet\Solr\Implementor\EventDispatcher;

class IndexField
{
    /**
     * @var Attribute
     */
    private $attribute;
    /**
     * @var boolean
     */
    private $forSorting;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param Attribute $attribute
     * @param EventDispatcher $eventDispatcher
     * @param boolean $forSorting
     */
    public function __construct(Attribute $attribute, EventDispatcher $eventDispatcher, $forSorting = false)
    {
        $this->attribute = $attribute;
        $this->forSorting = $forSorting;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function forSorting($forSorting = true)
    {
        return new self($this->attribute, $this->eventDispatcher, $forSorting);
    }

    public function getFieldNameForFullMatch()
    {
        return $this->getFieldNameWithTextFieldSuffix('_t_ns');
    }

    public function getFieldName()
    {
        return $this->getFieldNameWithTextFieldSuffix('_t');
    }

    /**
     * @return EventDispatcher
     */
    protected function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @param string $textFieldSuffix
     * @return string
     */
    private function getFieldNameWithTextFieldSuffix($textFieldSuffix)
    {
        $transportObject = new Transport(
            [
                'fieldname' => '',
            ]
        );

        $this->getEventDispatcher()->dispatch(
            'integernet_solr_get_fieldname',
            [
                'attribute' => $this->attribute,
                'transport' => $transportObject
            ]
        );

        if ($fieldName = $transportObject->getData('fieldname')) {
            return $fieldName;
        }

        if ($this->attribute->getUsedForSortBy() || $this->forSorting) {
            switch ($this->attribute->getBackendType()) {
                case Attribute::BACKEND_TYPE_DECIMAL:
                    return $this->attribute->getAttributeCode() . '_f';

                case Attribute::BACKEND_TYPE_INT:
                    if ($this->attribute->getFacetType() !== Attribute::FACET_TYPE_SELECT) {
                        return $this->attribute->getAttributeCode() . '_i';
                    }

                case Attribute::BACKEND_TYPE_TEXT:
                    return $this->attribute->getAttributeCode() . $textFieldSuffix;

                // fallthrough intended
                case Attribute::BACKEND_TYPE_VARCHAR:
                default:
                    if ($this->forSorting) {
                        return $this->attribute->getAttributeCode() . '_s';
                    }
                    return $this->attribute->getAttributeCode() . $textFieldSuffix;
            }
        } else {
            switch ($this->attribute->getBackendType()) {
                case Attribute::BACKEND_TYPE_DECIMAL:
                    return $this->attribute->getAttributeCode() . '_f_mv';

                case Attribute::BACKEND_TYPE_INT:
                    if ($this->attribute->getFacetType() != Attribute::FACET_TYPE_SELECT) {
                        return $this->attribute->getAttributeCode() . '_i_mv';
                    }

                // fallthrough intended
                case Attribute::BACKEND_TYPE_VARCHAR:
                case Attribute::BACKEND_TYPE_TEXT:
                default:
                    return $this->attribute->getAttributeCode() . $textFieldSuffix . '_mv';
            }
        }
    }
}