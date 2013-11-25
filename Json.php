<?php

namespace Docker;

class Json
{
    public static function encode($struct)
    {
        $struct = json_encode($struct, JSON_UNESCAPED_SLASHES);
        $struct = str_replace('[]', '{}', $struct);

        return $struct;
    }
}