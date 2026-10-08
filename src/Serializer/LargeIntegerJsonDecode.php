<?php

declare(strict_types=1);

namespace Docker\Serializer;

use Symfony\Component\Serializer\Encoder\JsonDecode;

/**
 * Decode JSON, clamping integers above PHP_INT_MAX to PHP_INT_MAX.
 *
 * Docker's uint64 fields can exceed PHP's integer range: `pids_stats.limit` is
 * 18446744073709551615 when a container has no PID limit. json_decode() turns
 * such values into floats, which the generated integer setters reject. Docker
 * uses them as "unlimited" markers or very large counters, so PHP_INT_MAX keeps
 * them usable without changing smaller values.
 */
class LargeIntegerJsonDecode extends JsonDecode
{
    public function decode(string $data, string $format, array $context = []): mixed
    {
        return self::clamp(parent::decode($data, $format, $context));
    }

    private static function clamp(mixed $value): mixed
    {
        if (\is_float($value)) {
            // (float) PHP_INT_MAX is exactly 2^63, the first value outside the integer range.
            return $value >= (float) \PHP_INT_MAX ? \PHP_INT_MAX : $value;
        }
        if (\is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = self::clamp($item);
            }
        } elseif ($value instanceof \stdClass) {
            foreach (get_object_vars($value) as $key => $item) {
                $value->{$key} = self::clamp($item);
            }
        }

        return $value;
    }
}
