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


final class Field
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var mixed
     */
    private $value;
    /**
     * @var float
     */
    private $boost;

    /**
     * @param string $name
     * @param mixed $value
     * @param float $boost
     */
    public function __construct($name, $value, $boost)
    {
        $this->name = $name;
        $this->value = $value;
        $this->boost = $boost;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @return float
     */
    public function boost()
    {
        return $this->boost;
    }
}