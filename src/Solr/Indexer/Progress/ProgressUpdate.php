<?php

namespace IntegerNet\Solr\Indexer\Progress;

/**
 * Interface for progress update events, emitted by the indexer. Can be handled by clients via status update callbacks.
 */
interface ProgressUpdate
{
    /**
     * @return string Unique identifier for given event, used to associate multiple updates (start, progress, finished)
     *                for the same event
     */
    public function getEventId();

    /**
     * @return string Description of the event, e.g. "Clearing index for store de_DE"
     */
    public function getDescription();

    /**
     * @return float Completion of the event, i.e. "0" for started event, "100" for completed event, anything in between
     * for progress updates
     */
    public function getPercentageCompleted();

    /**
     * @return float Unix timestamp of status update (with microseconds)
     */
    public function getTimestamp();

    /**
     * @return int Elapsed time of the event in milliseconds
     */
    public function getElapsedTimeMs();
}