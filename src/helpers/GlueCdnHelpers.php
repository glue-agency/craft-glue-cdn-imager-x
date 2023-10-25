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
        // Prepare url and query for parsing
        if(is_string($url)) {
            $url = [$url];
        }
        if(is_string($query)) {
            $query = explode('&', $query);
        }

        if(count($url) !== 2) {
            // @todo throw exception
        }

        // Add defaults to query
        if(! empty($defaultQuery = GlueTransformer::getInstance()->getSettings()->defaultParams)) {
            $query = array_merge($defaultQuery, $query);
        }

        // Extract the source image url
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

        // Sort query params
        ksort($query);

        // Build source url
        $transformUrl = http_build_url([
            'scheme' => $sourceScheme,
            'host'   => $sourceHost,
            'path'   => $sourcePath,
            'query'  => http_build_query($query),
        ]);

        // Generate the signature
        $signature = self::generateSignature($transformUrl);

        // Sign the url
        if(parse_url($transformUrl, PHP_URL_QUERY)) {
            $signedTransformUrl = $transformUrl . '&s=' . $signature;
        } else {
            $signedTransformUrl = $transformUrl . '?s=' . $signature;
        }

        return rtrim(reset($url), '/') . '/' . $signedTransformUrl;
    }

    public static function generateSignature(string $url)
    {
        $signKey = GlueTransformer::getInstance()->getSettings()->signKey;

        return md5("{$signKey}:{$url}");
    }
}
