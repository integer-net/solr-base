<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */

namespace IntegerNet\Solr\Response;


final class ApacheSolrDocument implements Document
{
    /**
     * @var \Apache_Solr_Document
     */
    private $apacheSolrDocument;

    public function __construct(\Apache_Solr_Document $apacheSolrDocument)
    {
        $this->apacheSolrDocument = $apacheSolrDocument;
    }

    /**
     * @param $name
     * @return Field
     */
    public function field($name)
    {
        return new Field(
            $name,
            $this->apacheSolrDocument->__get($name),
            $this->fieldBoost($name)
        );
    }

    /**
     * Returns field values as associative array
     *
     * @return mixed[]
     */
    public function asArray()
    {
        return \iterator_to_array($this->apacheSolrDocument);
    }

    /**
     * @param $name
     * @return float
     */
    private function fieldBoost($name)
    {
        $fieldBoost = $this->apacheSolrDocument->getFieldBoost($name);
        if ($fieldBoost === false) {
            return null;
        }
        return $fieldBoost;
    }

}