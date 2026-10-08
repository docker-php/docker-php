<?php

declare(strict_types=1);

namespace Docker\Tests;

use Docker\API\Model\ExecIdStartPostBody;
use Docker\Docker;
use Docker\DockerClientFactory;
use PHPUnit\Framework\Attributes\DataProvider;

class HttpClientDocumentationTest extends TransportTestCase
{
    public static function socketExamples(): iterable
    {
        yield 'Default socket' => ['guides/guzzle.mdx', 'Default socket', true];
        yield 'Bundled custom socket' => ['guides/guzzle.mdx', 'Bundled custom socket', false];
        yield 'Custom socket client' => ['reference/streams.mdx', 'Custom socket client', false];
    }

    #[DataProvider('socketExamples')]
    public function testDocumentedSocketClientStreamsTtyOutput(string $page, string $title, bool $useEnvironment): void
    {
        $address = $this->startServer(['responses' => [
            ['body' => '{}', 'headers' => ['Content-Type' => 'application/json']],
            [
                'headers' => ['Transfer-Encoding' => 'chunked', 'Content-Type' => 'application/vnd.docker.raw-stream'],
                'segments_base64' => [
                    base64_encode("7\r\nfirst\r\n\r\n"),
                    base64_encode("6\r\nlast\r\n\r\n0\r\n\r\n"),
                ],
                'delay_ms' => 1000,
            ],
        ]], true);
        putenv('DOCKER_HOST=unix://'.($useEnvironment ? $address : '/does-not-exist.sock'));
        $docker = $this->runDocumentationExample($page, $title, $address);
        $start = new ExecIdStartPostBody();
        $start->setDetach(false);
        $start->setTty(true);
        $stream = $docker->execStart('test', $start);
        $stdout = '';
        $firstCallback = null;
        $lastCallback = null;
        $stream->onStdout(static function (string $output) use (&$stdout, &$firstCallback, &$lastCallback): void {
            $firstCallback ??= hrtime(true);
            $lastCallback = hrtime(true);
            $stdout .= $output;
        });
        $stream->wait();

        self::assertSame("first\r\nlast\r\n", $stdout);
        self::assertNotNull($firstCallback);
        self::assertGreaterThan(500_000_000, $lastCallback - $firstCallback, 'Output was buffered until the peer finished.');
        self::assertStringStartsWith('GET /v'.DockerClientFactory::defaultApiVersion().'/info HTTP/1.1', $this->serverResult()['request']);
        $request = $this->serverResult(1)['request'];
        self::assertStringStartsWith('POST /v'.DockerClientFactory::defaultApiVersion().'/exec/test/start HTTP/1.1', $request);
        self::assertStringContainsString('Host: localhost', $request);
    }

    private function runDocumentationExample(string $page, string $title, string $address): Docker
    {
        $markdown = file_get_contents(\dirname(__DIR__).'/docs/'.$page);
        preg_match_all('/^```php ([^\r\n]+)\R(.*?)^```/ms', $markdown, $blocks);
        $examples = array_combine($blocks[1], $blocks[2]);
        self::assertArrayHasKey($title, $examples);
        $code = preg_replace('/^\s*<\?php\s*/', '', $examples[$title]);
        $code = str_replace("require __DIR__ . '/vendor/autoload.php';", '', $code);
        $code = str_replace('/run/custom/docker.sock', $address, $code);

        return (static fn (string $code): Docker => eval("namespace {\n".$code."\nreturn \$docker;\n}"))($code);
    }
}
