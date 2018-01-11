<?php

namespace IntegerNet\Solr\Indexer\Progress;

/**
 * Step counter for single event
 */
class EventStatus
{
    /**
     * @var int
     */
    private $stepsComplete;

    /**
     * @var int
     */
    private $stepsTotal;

    /**
     * @param int $stepsTotal
     */
    public function __construct($stepsTotal)
    {
        $this->stepsTotal = $stepsTotal;
    }

    public function advance($steps)
    {
        $this->stepsComplete += $steps;
    }

    /**
     * @return float
     */
    public function getPercentageComplete()
    {
        return $this->stepsComplete / $this->stepsTotal * 100.0;
    }

}