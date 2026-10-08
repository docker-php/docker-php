<?php

declare(strict_types=1);

namespace Docker\Tests\Stream;

use Docker\Stream\StatsStream;
use Nyholm\Psr7\Stream;
use PHPUnit\Framework\TestCase;

class StatsStreamTest extends TestCase
{
    private const BODY = "{\"read\":\"t1\",\"name\":\"/web {a}\",\"memory_stats\":{\"usage\":1}}\n"
        ."{\"read\":\"t2\",\"memory_stats\":{\"usage\":2}}\n";

    public function testDecodesEachSample(): void
    {
        $stream = new StatsStream(Stream::create(self::BODY));
        $usage = [];
        $stream->onFrame(static function (\stdClass $sample) use (&$usage): void {
            $usage[$sample->read] = $sample->memory_stats->usage;
        });

        $stream->wait();

        $this->assertSame(['t1' => 1, 't2' => 2], $usage);
    }

    public function testStopFromCallbackEndsWaitAndClosesBody(): void
    {
        $body = Stream::create(self::BODY);
        $stream = new StatsStream($body);
        $calls = [];
        $stream->onFrame(static function (\stdClass $sample) use ($stream, &$calls): void {
            $calls[] = 'first:'.$sample->read;
            $stream->stop();
        });
        $stream->onFrame(static function (\stdClass $sample) use (&$calls): void {
            $calls[] = 'second:'.$sample->read;
        });

        $stream->wait();

        $this->assertSame(['first:t1', 'second:t1'], $calls);
        $this->assertFalse($body->isReadable());
    }

    public function testStopBeforeWaitSkipsBody(): void
    {
        $stream = new StatsStream(Stream::create(self::BODY));
        $stream->onFrame(function (): void {
            $this->fail('No sample should be delivered after stop().');
        });

        $stream->stop();
        $stream->stop();
        $stream->wait();

        $this->addToAssertionCount(1);
    }

    public function testIgnoresIncompleteTrailingSample(): void
    {
        $stream = new StatsStream(Stream::create("{\"read\":\"t1\"}\n{\"read\":"));
        $reads = [];
        $stream->onFrame(static function (\stdClass $sample) use (&$reads): void {
            $reads[] = $sample->read;
        });

        $stream->wait();

        $this->assertSame(['t1'], $reads);
    }
}
