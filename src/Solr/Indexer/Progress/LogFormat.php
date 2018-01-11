<?php

namespace IntegerNet\Solr\Indexer\Progress;

/**
 * Single line string representation of progress update (tab separated)
 */
class LogFormat
{
    /**
     * @var ProgressUpdate
     */
    private $progressUpdate;

    /**
     * @param ProgressUpdate $progressUpdate
     */
    public function __construct(ProgressUpdate $progressUpdate)
    {
        $this->progressUpdate = $progressUpdate;
    }

    public function __toString()
    {
        return implode(
            "\t",
            [
                self::formatDateTimeWithMilliseconds($this->progressUpdate->getTimestamp()),
                $this->progressUpdate->getEventId(),
                $this->progressUpdate->getDescription(),
                $this->progressUpdate->getPercentageCompleted() . "%",
                $this->progressUpdate->getElapsedTimeMs() . "ms",
            ]
        );
    }

    /**
     * @param int|float $timestamp
     * @return string
     */
    private static function formatDateTimeWithMilliseconds($timestamp)
    {
        return \DateTime::createFromFormat('U.u', number_format($timestamp, 6, '.', ''))->format(DATE_RFC3339_EXTENDED);
    }
}