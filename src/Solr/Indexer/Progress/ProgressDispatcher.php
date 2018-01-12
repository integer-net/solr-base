<?php

namespace IntegerNet\Solr\Indexer\Progress;

/**
 * Creates progress updates and dispatches them to the configured progress handlers
 */
class ProgressDispatcher
{
    private $progressHandlers = [];

    /**
     * @var \SplStack
     */
    private $eventsInProgress;

    /**
     * @param ProgressHandler[] $progressHandlers
     */
    public function __construct(array $progressHandlers)
    {
        $this->progressHandlers = $progressHandlers;
        $this->eventsInProgress = new \SplStack();
    }

    public function info($eventDescription, $eventId = null, $timestamp = null)
    {
        $update = new EventInfo($eventDescription, $eventId, $timestamp);
        $this->dispatchUpdate($update);
    }

    public function start($eventDescription, $expectedSteps = 0, $eventId = null, $timestamp = null)
    {
        $update = new EventStart($eventDescription, $expectedSteps, $eventId, $timestamp);
        $this->eventsInProgress->push($update);
        $this->dispatchUpdate($update);
    }

    public function advance($steps = 1, $timestamp = null)
    {
        /** @var EventStart $currentEvent */
        $currentEvent = $this->eventsInProgress->top();
        $currentEvent->status()->advance($steps);
        $this->dispatchUpdate(
            new EventProgress($currentEvent, $steps, $timestamp)
        );
    }

    public function finish($timestamp = null)
    {
        $this->dispatchUpdate(
            new EventFinish(
                $this->eventsInProgress->pop(),
                $timestamp
            )
        );
    }

    private function dispatchUpdate($update)
    {
        foreach ($this->progressHandlers as $handler) {
            $handler->progress($update);
        }
    }
}