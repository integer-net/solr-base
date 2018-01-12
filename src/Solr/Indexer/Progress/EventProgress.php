<?php

namespace IntegerNet\Solr\Indexer\Progress;

class EventProgress implements ProgressUpdate
{
    /**
     * @var EventStart
     */
    private $eventStart;
    /**
     * @var int|null
     */
    private $timestamp;
    /**
     * @var int
     */
    private $steps;

    /**
     * @param EventStart $eventStart
     * @param int $steps
     * @param int|null $timestamp Timestamp. If null, current time is used.
     */
    public function __construct(EventStart $eventStart, $steps, $timestamp = null)
    {
        $this->eventStart = $eventStart;
        $this->steps = $steps;
        $this->timestamp = $timestamp ?: microtime(true);
    }

    public function getEventId()
    {
        return $this->eventStart->getEventId();
    }

    public function getDescription()
    {
        return $this->eventStart->getDescription();
    }

    public function getPercentageCompleted()
    {
        return $this->eventStart->status()->getPercentageComplete();
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getElapsedTimeMs()
    {
        return round(1000*($this->timestamp - $this->eventStart->getTimestamp()));
    }

    /**
     * @return int
     */
    public function getSteps()
    {
        return $this->steps;
    }
}