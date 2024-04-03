<?php

namespace GlueAgency\CDN\helpers;

use GlueAgency\CDN\GlueTransformer;

class GlueCdnHelpers
{

    public static function hasFocalPoints($transform): bool
    {
        return ! empty($transform['position']);
    }

    public static function buildFocalPoints($transform): string
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

    public static function buildUrl($url, $query = []): string
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

        // Encode the path url
        $encodedPathUrl = rawurlencode(end($url));

        // Generate the signature
        $signature = self::generateSignature($encodedPathUrl, $query);

        // Build the transform url
        $transformUrl = $encodedPathUrl . '?' . http_build_query($query);

        // Sign the url
        if(parse_url($transformUrl, PHP_URL_QUERY)) {
            $signedTransformUrl = $transformUrl . '&s=' . $signature;
        } else {
            $signedTransformUrl = $transformUrl . '?s=' . $signature;
        }

        return rtrim(reset($url), '/') . '/' . $signedTransformUrl;
    }

    public static function generateSignature(string $url, array $params): string
    {
        $signKey = GlueTransformer::getInstance()->getSettings()->signKey;

        ksort($params);

        return md5($signKey . ':' . $url . '?' . http_build_query(array_filter($params)));
    }
}
