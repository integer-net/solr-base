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


final class ApacheSolrDocumentCollection extends \ArrayIterator implements DocumentCollection
{
    public static function fromApacheSolrResponse(\Apache_Solr_Response $apacheSolrResponse)
    {
        return new static($apacheSolrResponse->response->docs);
    }

    /**
     * @return Document
     */
    public function current()
    {
        return $this->documentFromApacheSolrDocument(parent::current());
    }

    /**
     * @return Document
     */
    private function documentFromApacheSolrDocument(\Apache_Solr_Document $document)
    {
        return new ApacheSolrDocument($document);
    }

    /**
     * Returns each document as associative array
     *
     * @return mixed[][]
     */
    public function asArray()
    {
        return \array_map(function(Document $document) {
            return $document->asArray();
        }, \iterator_to_array($this));
    }

}