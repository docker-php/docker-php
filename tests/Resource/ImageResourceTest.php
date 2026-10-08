<?php

declare(strict_types=1);

namespace Docker\Tests\Resource;

use Docker\API\Client;
use Docker\API\Model\AuthConfig;
use Docker\Context\ContextBuilder;
use Docker\Tests\TestCase;

class ImageResourceTest extends TestCase
{
    /**
     * Return a container manager.
     */
    private function getManager()
    {
        return self::getDocker();
    }

    public function testBuild(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->from('ubuntu:precise');
        $contextBuilder->add('/test', 'test file content');

        $context = $contextBuilder->getContext();
        $buildStream = $this->getManager()->imageBuild($context->read(), ['t' => 'test-image']);

        $this->assertInstanceOf('Docker\Stream\BuildStream', $buildStream);

        $lastMessage = '';

        $buildStream->onFrame(function ($frame) use (&$lastMessage): void {
            $lastMessage = $frame->getStream();
        });
        $buildStream->wait();

        $this->assertStringContainsString('Successfully', $lastMessage);
    }

    public function testBuildAppliesSeveralTags(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->from('busybox:latest');
        $contextBuilder->add('/test', 'test file content');
        $context = $contextBuilder->getContext();
        $tags = ['docker-php-test-tags:one', 'docker-php-test-tags:two'];

        $buildStream = $this->getManager()->imageBuild($context->read(), ['t' => $tags]);
        $buildStream->wait();

        try {
            $repoTags = $this->getManager()->imageInspect($tags[0])->getRepoTags();
            sort($repoTags);
            $this->assertSame($tags, $repoTags);
        } finally {
            foreach ($tags as $tag) {
                $this->getManager()->imageDelete($tag);
            }
        }
    }

    public function testCreate(): void
    {
        $createImageStream = $this->getManager()->imageCreate('', [
            'fromImage' => 'registry:latest',
        ]);

        $this->assertInstanceOf('Docker\Stream\CreateImageStream', $createImageStream);

        $firstMessage = null;

        $createImageStream->onFrame(function ($createImageInfo) use (&$firstMessage): void {
            if (null === $firstMessage) {
                $firstMessage = $createImageInfo->getStatus();
            }
        });
        $createImageStream->wait();

        $this->assertStringContainsString('Pulling from library/registry', $firstMessage);
    }

    public function testPushStream(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->from('ubuntu:precise');
        $contextBuilder->add('/test', 'test file content');

        $context = $contextBuilder->getContext();
        $this->getManager()->imageBuild($context->read(), ['t' => 'localhost:5000/test-image'], [], Client::FETCH_OBJECT);

        $registryConfig = new AuthConfig();
        $registryConfig->setServeraddress('localhost:5000');
        $pushImageStream = $this->getManager()->imagePush('localhost:5000/test-image', [], [
            'X-Registry-Auth' => $registryConfig,
        ]);

        $this->assertInstanceOf('Docker\Stream\PushStream', $pushImageStream);

        $firstMessage = null;

        $pushImageStream->onFrame(function ($pushImageInfo) use (&$firstMessage): void {
            if (null === $firstMessage) {
                $firstMessage = $pushImageInfo->getStatus();
            }
        });
        $pushImageStream->wait();

        $this->assertStringContainsString('repository [localhost:5000/test-image]', $firstMessage);
    }
}
