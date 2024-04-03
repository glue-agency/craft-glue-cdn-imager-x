<?php

namespace GlueAgency\CDN\assetbundles;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * Purge Images Assets asset bundle
 */
class PurgeImagesAssets extends AssetBundle
{

    public function init(): void
    {
        $this->sourcePath = '@glue-cdn/assetbundles/dist';
        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/purge-images.js'
        ];

        $this->css = [
            'css/purge-images.css'
        ];

        parent::init();
    }
}
