<?php

namespace Docker;

/**
 * Docker\Json
 */
class Json
{
    /**
     * @param array $struct
     * 
     * @return string
     */
    public static function encode(array $struct)
    {
        $struct = json_encode($struct, JSON_UNESCAPED_SLASHES);
        $struct = str_replace('[]', '{}', $struct);

        return $struct;
    }
}