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
                self::formatDateTimeWithMicroseconds($this->progressUpdate->getTimestamp()),
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
    private static function formatDateTimeWithMicroseconds($timestamp)
    {
        // DATE_RFC3339_EXTENDED but with µs instead of ms for PHP 5.6 compatibility
        return \DateTime::createFromFormat('U.u', number_format($timestamp, 6, '.', ''))->format('Y-m-d\TH:i:s.uP');
    }
}