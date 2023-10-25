<?php

namespace GlueAgency\CDN\helpers;

use GlueAgency\CDN\GlueTransformer;

class GlueCdnHelpers
{

    public static function hasFocalPoints($transform)
    {
        return ! empty($transform['position']);
    }

    public static function buildFocalPoints($transform)
    {
        $position = $transform['position'] ?? [];
        [
            $x,
            $y,
        ] = explode(' ', $position);

        return implode('-', [
            (int) $x,
            (int) $y,
        ]);
    }

    public static function buildUrl($url, $query = null)
    {
        if(is_string($url)) {
            $url = [$url];
        }
        if(is_string($query)) {
            $query = explode('&', $query);
        }

        if(count($url) !== 2) {
            // @todo throw exception
        }

        if(! empty($defaultQuery = GlueTransformer::getInstance()->getSettings()->defaultParams)) {
            $query = array_merge($defaultQuery, $query);
        }

        [
            'scheme' => $sourceScheme,
            'host'   => $sourceHost,
            'path'   => $sourcePath,
            'query'  => $sourceQuery,
        ] = parse_url(end($url)) + ['query' => ''];

        if($sourceQuery) {
            parse_str($sourceQuery, $result);

            $query = array_merge($result, $query);
        }

        return rtrim(reset($url), '/') . '/' . http_build_url([
            'scheme' => $sourceScheme,
            'host'   => $sourceHost,
            'path'   => $sourcePath,
            'query'  => http_build_query($query),
        ]);
    }
}
