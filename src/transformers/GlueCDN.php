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

    public function transform(Asset|string $image, array $transforms): ?array
    {
        $transformedImages = [];

        foreach($transforms as $transform) {
            $transformedImages[] = $this->getTransformedImage($image, $transform);
        }

        return $transformedImages;
    }

    private function getTransformedImage(Asset|string $image, array $transform): GlueCDNTransformedImageModel
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

        // Transforms
        if(isset($transform['width'])) {
            $query['w'] = $transform['width'];
        }

        if(isset($transform['height'])) {
            $query['h'] = $transform['height'];
        }

        if(isset($transform['flip'])) {
            // @todo 'h' will not work because it is considered a callable
            // We need to remap h => horizontal and than back to h for the url builder.
            // Might be best to move all transform keys to a separate builder

            if($transform['flip'] == 'horizontal' || $transform['flip'] == 'h') {
                $query['flip'] = 'h';
            }

            if($transform['flip'] == 'vertical' || $transform['flip'] == 'v') {
                $query['flip'] = 'v';
            }

            if($transform['flip'] == 'both') {
                $query['flip'] = 'both';
            }
        }

        if(isset($transform['dpr'])) {
            $query['dpr'] = $transform['dpr'];
        }

        if(isset($transform['bgColor'])) {
            $query['bg'] = $transform['bgColor'];
        }

        if(isset($transform['border']) && $border = $transform['border']) {
            if(isset($border['width']) && isset($border['color'])) {
                $width = $border['width'];
                $color = $border['color'];
                $method = $border['method'] ?? 'expand';

                $query['border'] = "{$width},{$color},{$method}";
            }
        }

        // Effects
        if(isset($transform['effects']) && $effects = $transform['effects']) {

            if(isset($effects['brightness'])) {
                $query['bri'] = $effects['brightness'];
            }

            if(isset($effects['contrast'])) {
                $query['con'] = $effects['contrast'];
            }

            if(isset($effects['gamma'])) {
                $query['gam'] = $effects['gamma'];
            }

            if(isset($effects['sharpen'])) {
                $query['sharp'] = $effects['sharpen'];
            }

            if(isset($effects['blur'])) {
                $query['blur'] = $effects['blur'];
            }

            if(isset($effects['pixelate'])) {
                $query['pixel'] = $effects['pixelate'];
            }

            if(isset($effects['grayscale'])) {
                if(!! $effects['grayscale']) {
                    $query['filt'] = 'greyscale';
                }

                if(!! $effects['sepia']) {
                    $query['filt'] = 'sepia';
                }
            }
        }

        // Mode
        $mode = $transform['mode'] ?? 'crop';

        switch($mode) {
            case 'stretch':
            case 'croponly':
            case 'letterbox':
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

        return new GlueCDNTransformedImageModel($url, $image, $transform, $glueCdnConfig);
    }
}
