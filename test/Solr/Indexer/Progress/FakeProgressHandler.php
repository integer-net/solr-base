<?php

namespace IntegerNet\Solr\Indexer\Progress;

/**
 * Fake progress handler for tests, just collects the updates for inspection
 */
class FakeProgressHandler implements ProgressHandler
{
    public $updates = [];
    public $updatesAsString = [];

    public function progress(ProgressUpdate $update)
    {
        $this->updates[] = $update;
        $this->updatesAsString[] = (string) new LogFormat($update);
    }
}