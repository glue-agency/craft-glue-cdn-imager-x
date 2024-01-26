<?php

namespace GlueAgency\CDN\models;

use craft\elements\Asset;
use spacecatninja\imagerx\helpers\ImagerHelpers;
use spacecatninja\imagerx\models\BaseTransformedImageModel;
use spacecatninja\imagerx\models\LocalSourceImageModel;
use spacecatninja\imagerx\models\TransformedImageInterface;
use GlueAgency\CDN\models\Settings as GlueCdnSettings;

class GlueCDNTransformedImageModel extends BaseTransformedImageModel implements TransformedImageInterface
{

    protected GlueCdnSettings|null $config;

    public function __construct(?string $imageUrl = null, Asset|string $source = null, array $transform = [], ?GlueCdnSettings $config = null)
    {
        $this->config = $config;

        if($imageUrl !== null) {
            $this->url = $imageUrl;
        }

        $mode = $transform['mode'] ?? 'crop';

        if(isset($transform['width'], $transform['height'])) {
            $this->width = (int) $transform['width'];
            $this->height = (int) $transform['height'];

            if($source !== null && $mode === 'fit') {
                [$sourceWidth, $sourceHeight] = $this->getSourceImageDimensions($source);

                $transformW = (int) $transform['width'];
                $transformH = (int) $transform['height'];

                if($sourceWidth !== 0 && $sourceHeight !== 0) {
                    if($sourceWidth / $sourceHeight > $transformW / $transformH) {
                        $useW = min($transformW, $sourceWidth);
                        $this->width = $useW;
                        $this->height = round($useW * ($sourceHeight / $sourceWidth));
                    } else {
                        $useH = min($transformH, $sourceHeight);
                        $this->width = round($useH * ($sourceWidth / $sourceHeight));
                        $this->height = $useH;
                    }
                }
            }
        } else if(isset($transform['width']) || isset($transform['height'])) {
            if($source !== null && $transform !== null) {
                [$sourceWidth, $sourceHeight,] = $this->getSourceImageDimensions($source);
                if ((int)$sourceWidth === 0 || (int)$sourceHeight === 0) {
                    if (isset($params['w'])) {
                        $this->width = (int)$params['w'];
                    }
                    if (isset($params['h'])) {
                        $this->height = (int)$params['h'];
                    }
                } else {
                    [$w, $h,] = $this->calculateTargetSize($transform, $sourceWidth, $sourceHeight);

                    $this->width = $w;
                    $this->height = $h;
                }
            }
        } else {
            // Neither is set, image is not resized. Just get dimensions and return.
            [
                $sourceWidth,
                $sourceHeight,
            ] = $this->getSourceImageDimensions($source);

            $this->width = $sourceWidth;
            $this->height = $sourceHeight;
        }
    }

    protected function getSourceImageDimensions(Asset|string $source): array
    {
        if ($source instanceof Asset) {
            return [$source->getWidth(), $source->getHeight()];
        }

        if($this->config->getExternalImageDimensions) {
            $sourceModel = new LocalSourceImageModel($source);
            $sourceModel->getLocalCopy();

            $sourceImageSize = ImagerHelpers::getSourceImageSize($sourceModel);

            return [$sourceImageSize[0], $sourceImageSize[1]];

        }

        return [0, 0];
    }

    protected function calculateTargetSize(array $transform, int $sourceWidth, int $sourceHeight): array
    {
        $ratio = $sourceWidth / $sourceHeight;

        $w = $transform['width'] ?? null;
        $h = $transform['height'] ?? null;

        if($w) {
            return [
                $w,
                round($w / $ratio),
            ];
        }
        if($h) {
            return [
                round($h * $ratio),
                $h,
            ];
        }

        return [
            0,
            0,
        ];
    }
}
