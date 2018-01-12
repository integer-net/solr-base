<?php
namespace IntegerNet\Solr\Indexer\Progress;

class ProgressUpdateTest extends \PHPUnit\Framework\TestCase
{
    public function testStartAndFinishEvent()
    {
        $eventId = 'xxxxxx';
        $startTime = strtotime('2001-02-03 04:05:06');
        $endTime = $startTime + 0.05;
        $eventDescription = 'Running test';

        $handler = new FakeProgressHandler();
        $progress = new ProgressDispatcher([$handler]);
        $progress->start($eventDescription, 0, $eventId, $startTime);
        $progress->finish($endTime);

        $this->assertEquals(
            [
                "2001-02-03T04:05:06.000000+00:00\t$eventId\t$eventDescription\t0%\t0ms",
                "2001-02-03T04:05:06.050000+00:00\t$eventId\t$eventDescription\t100%\t50ms",
            ],
            $handler->updatesAsString
        );
    }
    public function testEventWithProgress()
    {
        $eventId = 'xxxxxx';
        $startTime = strtotime('2001-02-03 04:05:06');
        $endTime = $startTime + 0.01;
        $eventDescription = 'Running test';

        $handler = new FakeProgressHandler();
        $progress = new ProgressDispatcher([$handler]);
        $progress->start($eventDescription, 2, $eventId, $startTime);
        $progress->advance(1, $startTime);
        $progress->advance(1, $endTime);
        $progress->finish($endTime);

        $this->assertEquals(
            [
                "2001-02-03T04:05:06.000000+00:00\t$eventId\t$eventDescription\t0%\t0ms",
                "2001-02-03T04:05:06.000000+00:00\t$eventId\t$eventDescription\t50%\t0ms",
                "2001-02-03T04:05:06.010000+00:00\t$eventId\t$eventDescription\t100%\t10ms",
                "2001-02-03T04:05:06.010000+00:00\t$eventId\t$eventDescription\t100%\t10ms",
            ],
            $handler->updatesAsString
        );
    }
}