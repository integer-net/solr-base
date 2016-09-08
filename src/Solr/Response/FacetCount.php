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


final class FacetCount
{
    private $value;
    private $count;

    /**
     * @param string $value
     * @param int $count
     */
    public function __construct($value, $count)
    {
        $this->value = $value;
        $this->count = $count;
    }
    public function value()
    {
        return $this->value;
    }
    public function count()
    {
        return $this->count;
    }
    /**
     * @param string $keyValue
     * @param string $keyCount
     * @return mixed[]
     */
    public function toArray($keyValue = 'value', $keyCount = 'count')
    {
        return [
            $keyValue => $this->value,
            $keyCount => $this->count,
        ];
    }

    /**
     * @param \Closure $modifier
     * @return FacetCount
     */
    public function withModifiedValue(\Closure $modifier)
    {
        return new static($modifier($this->value), $this->count);
    }

}