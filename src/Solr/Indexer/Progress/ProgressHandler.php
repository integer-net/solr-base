<?php

namespace IntegerNet\Solr\Indexer\Progress;

/**
 * Progress handler to be implemented by client, e.g. CLI output
 */
interface ProgressHandler
{
    public function progress(ProgressUpdate $update);
}