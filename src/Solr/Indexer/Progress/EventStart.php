<?php

namespace IntegerNet\Solr\Indexer\Progress;

class EventStart implements ProgressUpdate
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
    private $startTime;
    /**
     * @var int
     */
    private $expectedSteps;

    /**
     * @var EventStatus
     */
    private $status;

    /**
     * @param string $description
     * @param int $expectedSteps
     * @param string|null $eventId Unique event ID. If null, will be generated.
     * @param int|null $timestamp Timestamp. If null, current time is used.
     */
    public function __construct($description, $expectedSteps = 0, $eventId = null, $timestamp = null)
    {
        $this->description = $description;
        $this->eventId = $eventId ?: sha1(uniqid('', true));
        $this->startTime = $timestamp ?: microtime(true);
        $this->expectedSteps = $expectedSteps;
        $this->status = new EventStatus($this->expectedSteps);
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
        return $this->startTime;
    }

    public function getPercentageCompleted()
    {
        return 0;
    }

    public function getElapsedTimeMs()
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getExpectedSteps()
    {
        return $this->expectedSteps;
    }

    /**
     * @return EventStatus
     */
    public function status()
    {
        return $this->status;
    }
}