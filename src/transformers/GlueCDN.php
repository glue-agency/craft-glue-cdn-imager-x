<?php

namespace GlueAgency\CDN\transformers;

use craft\base\Component;
use craft\elements\Asset;

use GlueAgency\CDN\GlueTransformer;
use GlueAgency\CDN\helpers\GlueCdnHelpers;
use GlueAgency\CDN\models\GlueCDNTransformedImageModel;
use spacecatninja\imagerx\transformers\TransformerInterface;
use spacecatninja\imagerx\exceptions\ImagerException;

class GlueCDN extends Component implements TransformerInterface
{

    /**
     * @param Asset $image
     * @param array $transforms
     *
     * @throws ImagerException
     *
     * @return array|null
     */
    public function transform($image, $transforms)
    {
        $transformedImages = [];

        foreach($transforms as $transform) {
            $transformedImages[] = $this->getTransformedImage($image, $transform);
        }

        return $transformedImages;
    }

    /**
     * @param Asset|string $image
     * @param array        $transform
     *
     * @throws ImagerException
     *
     * @return GlueCDNTransformedImageModel
     */
    private function getTransformedImage($image, $transform)
    {
        $glueCdnConfig = GlueTransformer::getInstance()->getSettings();

        // Get the image or asset url
        $imageUrl = $image instanceof Asset ? $image->getUrl() : $image;

        // Build url segments
        $urlSegments = [];

        $urlSegments[] = $glueCdnConfig->baseUrl;
        $urlSegments[] = $imageUrl;

        // Build the querystring parameters
        $query = [];

        if(isset($transform['width'])) {
            $query['w'] = $transform['width'];
        }

        if(isset($transform['height'])) {
            $query['h'] = $transform['height'];
        }

        $mode = $transform['mode'] ?? 'crop';

        switch($mode) {
            case 'stretch':
            case 'croponly':
                // not supported

                break;
            case 'fit':
                $query['fit'] = 'fill';

                break;
            default:
                $query['fit'] = 'crop';

                if(GlueCdnHelpers::hasFocalPoints($transform)) {
                    $query['fit'] = 'crop-' . GlueCdnHelpers::buildFocalPoints($transform);
                }

                break;
        }

        $url = GlueCdnHelpers::buildUrl($urlSegments, $query);

        // Make secure if signToken is set
//        if (!empty($profile->signToken)) {
//            $bossToken = hash_hmac('sha256', parse_url($url, PHP_URL_PATH), $profile->signToken);
//            $url .= "?bossToken=$bossToken";
//        }

        return new GlueCDNTransformedImageModel($url, $image, $transform, $glueCdnConfig);
    }
}
