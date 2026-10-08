<?php

declare(strict_types=1);

namespace Docker\Tests;

use Docker\Docker;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Execute selected MDX recipes against a fake transport, never a Docker daemon.
 */
class DocumentationExamplesTest extends TestCase
{
    private string $fixtureDirectory;
    private array $requests = [];

    protected function setUp(): void
    {
        $this->fixtureDirectory = sys_get_temp_dir().'/docker-php-docs-'.bin2hex(random_bytes(12));
        $fs = new Filesystem();
        $fs->mkdir($this->fixtureDirectory.'/build');
        $fs->dumpFile($this->fixtureDirectory.'/build/Dockerfile', "FROM busybox:latest\nCMD echo hello\n");
        $fs->dumpFile($this->fixtureDirectory.'/upload.tar', "upload fixture\0");
        $fs->dumpFile($this->fixtureDirectory.'/saved-image.tar', "image fixture\0");
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->fixtureDirectory);
    }

    private function client(callable $respond): Docker
    {
        $record = function (RequestInterface $request) use ($respond): ResponseInterface {
            if ('/info' === $request->getUri()->getPath()) {
                return new Response(200, ['Content-Type' => 'application/json'], '{}');
            }
            $this->requests[] = [
                'method' => $request->getMethod(),
                'path' => $request->getUri()->getPath(),
                'query' => $request->getUri()->getQuery(),
                'headers' => $request->getHeaders(),
                'body' => $request->getBody()->getContents(),
            ];

            return $respond($request);
        };
        $httpClient = new class($record) implements ClientInterface {
            public function __construct(private $respond)
            {
            }

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return ($this->respond)($request);
            }
        };

        return Docker::create($httpClient, [], [], false);
    }

    private function runExample(string $page, int $index, Docker $client, array $variables = []): string
    {
        $markdown = file_get_contents(\dirname(__DIR__).'/docs/'.$page.'.mdx');
        preg_match_all('/^```php[^\r\n]*\R(.*?)^```/ms', $markdown, $blocks);
        self::assertArrayHasKey($index, $blocks[1]);
        // Fragments reuse the imports from the complete example on their page.
        preg_match_all('/^use [^;]+;$/m', implode("\n", $blocks[1]), $imports);
        $code = preg_replace('/^\s*<\?php\s*/', '', $blocks[1][$index]);
        $code = preg_replace('/^use [^;]+;$/m', '', $code);
        $code = str_replace("require __DIR__ . '/vendor/autoload.php';", '', $code);
        $code = str_replace('$docker = Docker::create();', '$docker = $documentationClient;', $code);
        $code = str_replace('getenv(\'DOCKER_REGISTRY_USER\')', "'documentation-user'", $code);
        $code = str_replace('getenv(\'DOCKER_REGISTRY_TOKEN\')', "'documentation-token'", $code);
        $code = str_replace('__DIR__', var_export($this->fixtureDirectory, true), $code);
        self::assertStringNotContainsString('Docker::create(', $code, 'Examples must use the fake transport');
        $code = "namespace {\n".implode("\n", array_unique($imports[0]))."\n".$code."\n}";

        ob_start();
        try {
            (static function (Docker $documentationClient, array $variables, string $code): void {
                $docker = $documentationClient;
                $username = 'documentation-user';
                $token = 'documentation-token';
                extract($variables, \EXTR_SKIP);
                eval($code);
            })($client, $variables, $code);

            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    public function testContainerConfigurationPreservesNestedJsonShapes(): void
    {
        $client = $this->client(static fn () => new Response(201, ['Content-Type' => 'application/json'], '{"Id":"created","Warnings":[]}'));
        self::assertStringContainsString('Created created', $this->runExample('cookbook/container-config', 0, $client));
        $payload = json_decode($this->requests[0]['body'], false, 512, \JSON_THROW_ON_ERROR);
        self::assertInstanceOf(\stdClass::class, $payload->ExposedPorts->{'80/tcp'});
        self::assertSame('8080', $payload->HostConfig->PortBindings->{'80/tcp'}[0]->HostPort);
        self::assertSame('127.0.0.1', $payload->HostConfig->PortBindings->{'80/tcp'}[0]->HostIp);
        self::assertTrue($payload->HostConfig->Mounts[0]->ReadOnly);
        self::assertSame(['EXAMPLE_MODE=development'], $payload->Env);
        self::assertSame('POST', $this->requests[0]['method']);
        self::assertSame('/containers/create', $this->requests[0]['path']);
    }

    public function testPullUsesAnEncodedHeaderAndConsumesProgress(): void
    {
        $client = $this->client(static fn () => new Response(200, ['Content-Type' => 'application/json'], "{\"status\":\"Pulling\"}\n{\"status\":\"Done\"}\n"));
        $output = $this->runExample('guides/registry', 0, $client);
        self::assertStringContainsString('Pulling', $output);
        self::assertStringContainsString('Done', $output);
        $header = $this->requests[0]['headers']['X-Registry-Auth'][0];
        $auth = json_decode(base64_decode(strtr($header, '-_', '+/'), true), true, 512, \JSON_THROW_ON_ERROR);
        self::assertSame('documentation-token', $auth['password']);
        parse_str($this->requests[0]['query'], $query);
        self::assertSame('registry.example.com/team/app', $query['fromImage']);
        self::assertSame('example', $query['tag']);
    }

    public function testPullReportsAnErrorInsideAnHttp200Stream(): void
    {
        $client = $this->client(static fn () => new Response(200, ['Content-Type' => 'application/json'], '{"errorDetail":{"message":"pull denied"},"error":"pull denied"}'));
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('pull denied');
        $this->runExample('guides/registry', 0, $client);
    }

    public function testPushConvertsTheAuthModel(): void
    {
        $client = $this->client(static fn (RequestInterface $request) => str_ends_with($request->getUri()->getPath(), '/tag')
            ? new Response(201)
            : new Response(200, ['Content-Type' => 'application/json'], '{"status":"Pushed"}'));
        self::assertStringContainsString('Pushed', $this->runExample('guides/registry', 1, $client));
        self::assertCount(2, $this->requests);
        $auth = json_decode(base64_decode($this->requests[1]['headers']['X-Registry-Auth'][0], true), true, 512, \JSON_THROW_ON_ERROR);
        self::assertSame('documentation-user', $auth['username']);
        self::assertSame('registry.example.com', $auth['serveraddress']);
    }

    public static function buildExamples(): iterable
    {
        // Index 1 is the one-line .dockerignore opt-in, which does not build.
        yield 'directory context' => [0];
        yield 'query options' => [2];
        yield 'generated context' => [3];
        yield 'private base image' => [4];
    }

    /** @dataProvider buildExamples */
    public function testBuildExamplesSendTarAndReadFrames(int $index): void
    {
        $client = $this->client(static fn () => new Response(200, ['Content-Type' => 'application/json'], '{"stream":"Built successfully"}'));
        self::assertStringContainsString('Built successfully', $this->runExample('cookbook/build-image', $index, $client));
        self::assertSame('/build', $this->requests[0]['path']);
        self::assertStringContainsString('Dockerfile', $this->requests[0]['body']);
        if (2 === $index) {
            parse_str($this->requests[0]['query'], $query);
            self::assertSame(['APP_MODE' => 'development'], json_decode($query['buildargs'], true, 512, \JSON_THROW_ON_ERROR));
            self::assertSame('true', $query['pull']);
        }
        if (4 === $index) {
            $config = json_decode(base64_decode($this->requests[0]['headers']['X-Registry-Config'][0], true), true, 512, \JSON_THROW_ON_ERROR);
            self::assertSame('documentation-token', $config['registry.example.com']['password']);
        }
    }

    public function testArchiveDownloadPreservesBinaryBytes(): void
    {
        $bytes = "archive\0\xff".str_repeat('x', 70000);
        $client = $this->client(static fn () => new Response(200, ['Content-Type' => 'application/x-tar'], $bytes));
        $this->runExample('guides/archives', 0, $client);
        self::assertSame($bytes, file_get_contents($this->fixtureDirectory.'/container-etc.tar'));
    }

    public function testArchiveDownloadChecksStatusBeforeCreatingAFile(): void
    {
        $client = $this->client(static fn () => new Response(404, ['Content-Type' => 'application/json'], '{"message":"not found"}'));
        try {
            $this->runExample('guides/archives', 0, $client);
            self::fail('Expected the raw HTTP error to be rejected');
        } catch (\RuntimeException $error) {
            self::assertStringContainsString('HTTP 404', $error->getMessage());
            self::assertFileDoesNotExist($this->fixtureDirectory.'/container-etc.tar');
        }
    }

    public function testArchiveMetadataComesFromTheHeader(): void
    {
        $client = $this->client(static fn () => new Response(200, ['X-Docker-Container-Path-Stat' => base64_encode('{"name":"etc"}')], ''));
        self::assertSame("etc\n", $this->runExample('guides/archives', 1, $client));
        self::assertSame('HEAD', $this->requests[0]['method']);
    }

    public function testArchiveUploadStreamsTheInputAndUsesStringOptions(): void
    {
        $client = $this->client(static fn () => new Response(200));
        $this->runExample('guides/archives', 2, $client);
        self::assertSame("upload fixture\0", $this->requests[0]['body']);
        self::assertSame('PUT', $this->requests[0]['method']);
        parse_str($this->requests[0]['query'], $query);
        self::assertSame('true', $query['noOverwriteDirNonDir']);
        self::assertSame('/tmp', $query['path']);
    }

    public function testImageLoadReadsMultipleProgressDocuments(): void
    {
        $client = $this->client(static fn () => new Response(200, ['Content-Type' => 'application/json'], "{\"stream\":\"Loaded one\\n\"}\n{\"stream\":\"Loaded two\\n\"}\n"));
        self::assertSame("Loaded one\nLoaded two\n", $this->runExample('guides/image-transfer', 0, $client));
        self::assertSame("image fixture\0", $this->requests[0]['body']);
    }

    public function testImageLoadDetectsAnErrorInsideHttp200(): void
    {
        $client = $this->client(static fn () => new Response(200, ['Content-Type' => 'application/json'], '{"errorDetail":{"message":"invalid archive"}}'));
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('invalid archive');
        $this->runExample('guides/image-transfer', 0, $client);
    }

    public function testEventsUseStringTimestampsAndModelCallbacks(): void
    {
        $client = $this->client(static fn () => new Response(200, ['Content-Type' => 'application/json'], '{"time":123,"Action":"start","Actor":{"ID":"example"}}'));
        self::assertSame("123\tstart\texample\n", $this->runExample('guides/events-and-stats', 0, $client));
        parse_str($this->requests[0]['query'], $query);
        self::assertSame(60, (int) $query['until'] - (int) $query['since']);
        self::assertSame(['type' => ['container']], json_decode($query['filters'], true, 512, \JSON_THROW_ON_ERROR));
    }

    public function testStatsExplicitlyDisableStreaming(): void
    {
        if (!class_exists('Docker\\API\\Model\\ContainerStatsResponse')) {
            self::markTestSkipped('The stats example uses the model added in API 1.48.');
        }
        $client = $this->client(static fn () => new Response(200, ['Content-Type' => 'application/json'], '{"memory_stats":{"usage":1024}}'));
        self::assertSame("Memory usage: 1024 bytes\n", $this->runExample('guides/events-and-stats', 1, $client));
        parse_str($this->requests[0]['query'], $query);
        self::assertSame('0', $query['stream']);
    }

    public function testJsonQueryParametersAreEncodedOnce(): void
    {
        $client = $this->client(static fn () => new Response(200, ['Content-Type' => 'application/json'], '[]'));
        $this->runExample('reference/requests-and-responses', 0, $client);
        parse_str($this->requests[0]['query'], $query);
        self::assertSame(['label' => ['docker-php-example=true'], 'status' => ['running']], json_decode($query['filters'], true, 512, \JSON_THROW_ON_ERROR));
    }

    public function testBuildReportsDaemonErrorsAfterHttpSuccess(): void
    {
        $client = $this->client(static fn () => new Response(200, ['Content-Type' => 'application/json'], '{"errorDetail":{"message":"build failed"},"error":"build failed"}'));
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('build failed');
        $this->runExample('cookbook/build-image', 0, $client);
    }

    public function testLineBufferHandlesSplitLinesAndAnUnterminatedLastLine(): void
    {
        $frames = pack('CxxxN', 1, 3).'hel'.pack('CxxxN', 1, 7)."lo\nlast";
        $client = $this->client(static fn () => new Response(200, ['Content-Type' => 'application/vnd.docker.multiplexed-stream'], $frames));
        self::assertSame("line: hello\nlast line: last\n", $this->runExample('guides/logs', 2, $client));
    }

    public function testExecCreatesAndStartsWithConsistentTtySettings(): void
    {
        $client = $this->client(static fn (RequestInterface $request) => str_ends_with($request->getUri()->getPath(), '/exec')
            ? new Response(201, ['Content-Type' => 'application/json'], '{"Id":"example-exec"}')
            : new Response(101, ['Content-Type' => 'application/vnd.docker.multiplexed-stream'], pack('CxxxN', 1, 6)."hello\n"));
        self::assertSame("hello\n", $this->runExample('guides/exec', 0, $client));
        $create = json_decode($this->requests[0]['body'], true, 512, \JSON_THROW_ON_ERROR);
        $start = json_decode($this->requests[1]['body'], true, 512, \JSON_THROW_ON_ERROR);
        self::assertFalse($create['Tty']);
        self::assertFalse($start['Tty']);
        self::assertFalse($start['Detach']);
    }

    public function testExecChecksTheCommandExitCode(): void
    {
        $client = $this->client(static fn () => new Response(200, ['Content-Type' => 'application/json'], '{"Running":false,"ExitCode":7}'));
        $exec = new \Docker\API\Model\IdResponse();
        $exec->setId('example-exec');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('exit status 7');
        $this->runExample('guides/exec', 1, $client, ['exec' => $exec]);
    }

    public function testRawResponseExampleChecksTheHttpStatus(): void
    {
        $client = $this->client(static fn () => new Response(500));
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('HTTP 500');
        $this->runExample('reference/requests-and-responses', 1, $client);
    }

    public function testTypedErrorExampleUsesTheDaemonMessage(): void
    {
        $client = $this->client(static fn () => new Response(404, ['Content-Type' => 'application/json'], '{"message":"example missing"}'));
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('example missing');
        $this->runExample('reference/requests-and-responses', 2, $client);
    }
}
