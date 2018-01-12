<?php

namespace IntegerNet\Solr\Indexer\Progress;

class EventFinish implements ProgressUpdate
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
     * @param EventStart $eventStart
     * @param int|null $timestamp Timestamp. If null, current time is used.
     */
    public function __construct(EventStart $eventStart, $timestamp = null)
    {
        $this->eventStart = $eventStart;
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
        return 100;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getElapsedTimeMs()
    {
        return round(1000*($this->timestamp - $this->eventStart->getTimestamp()));
    }

}