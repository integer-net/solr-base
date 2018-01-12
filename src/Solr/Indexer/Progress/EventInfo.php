<?php

namespace IntegerNet\Solr\Indexer\Progress;

class EventInfo implements ProgressUpdate
{
    /**
     * @var string
     */
    private $eventId;

    /**
     * @var string
     */
    private $description;
    /**
     * @var int|null
     */
    private $timestamp;


    /**
     * @param string $description
     * @param string|null $eventId Unique event ID. If null, will be generated.
     * @param int|null $timestamp Timestamp. If null, current time is used.
     */
    public function __construct($description, $eventId = null, $timestamp = null)
    {
        $this->description = $description;
        $this->eventId = $eventId ?: sha1(uniqid('', true));
        $this->timestamp = $timestamp ?: microtime(true);
    }

    public function getEventId()
    {
        return $this->eventId;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getPercentageCompleted()
    {
        return 100;
    }

    public function getElapsedTimeMs()
    {
        return 0;
    }

}