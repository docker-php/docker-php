<?php

namespace Docker\Tests\Helper;

use Docker\Context\ContextBuilder;
use Docker\Helper\BuilderHelper;
use Docker\Stream\BuildFrame;
use Docker\Tests\TestCase;
use Http\Message\StreamFactory\GuzzleStreamFactory;

class BuilderHelperTest extends TestCase
{
    /** @var BuilderHelper */
    private $buildHelper;

    public function setUp()
    {
        $this->buildHelper = new BuilderHelper($this->getDocker()->getImageManager(), new GuzzleStreamFactory());
    }

    public function testBuild()
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->from('ubuntu:precise');
        $contextBuilder->add('/test', 'test file content');

        $buildStream = $this->buildHelper->build($contextBuilder->getContext(), 'test-image');
        $lastMessage = "";

        $buildStream->onFrame(function (BuildFrame $frame) use (&$lastMessage) {
            $lastMessage = $frame->getStream();
        });

        $buildStream->wait();

        $this->assertContains("Successfully built" ,$lastMessage);
    }
}
