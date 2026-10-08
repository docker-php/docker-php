<?php

declare(strict_types=1);

namespace Docker\Tests\Serializer;

use Docker\Serializer\LargeIntegerJsonDecode;
use PHPUnit\Framework\TestCase;

class LargeIntegerJsonDecodeTest extends TestCase
{
    public function testClampsIntegersAbovePhpRange(): void
    {
        $json = '{"limit":18446744073709551615,"current":7,"ratio":0.5,"items":[9223372036854775808,1],"name":"18446744073709551615"}';

        $associative = (new LargeIntegerJsonDecode(['json_decode_associative' => true]))->decode($json, 'json');
        $object = (new LargeIntegerJsonDecode())->decode($json, 'json');

        $this->assertSame(['limit' => \PHP_INT_MAX, 'current' => 7, 'ratio' => 0.5, 'items' => [\PHP_INT_MAX, 1], 'name' => '18446744073709551615'], $associative);
        $this->assertSame(\PHP_INT_MAX, $object->limit);
        $this->assertSame([\PHP_INT_MAX, 1], $object->items);
        $this->assertSame(0.5, $object->ratio);
    }
}
