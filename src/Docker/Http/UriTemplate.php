<?php

namespace Docker\Http;

/**
 * Docker\Http\UriTemplate
 * 
 * Very limited support for RFC 6570
 * Only supports basic {foo} and {?foo*} expansions.
 * 
 * @see http://tools.ietf.org/html/rfc6570
 */
class UriTemplate
{
    public function compile($template, $vars)
    {
        $uri = $template;

        $uri = preg_replace_callback('/{\?(.+?)\*}/', function($matches) use ($vars) {
            return '?'.http_build_query($vars[$matches[1]]);
        }, $uri);

        $uri = preg_replace_callback('/{(.+?)}/', function($matches) use ($vars) {
            return $vars[$matches[1]];
        }, $uri);

        return $uri;
    }
}