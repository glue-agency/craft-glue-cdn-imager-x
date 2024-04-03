<?php

namespace GlueAgency\CDN\utilities;

use Craft;
use craft\base\Utility;
use GlueAgency\CDN\assetbundles\PurgeImagesAssets;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * Purge Images utility
 */
class PurgeImages extends Utility
{
    public static function displayName(): string
    {
        return Craft::t('glue-cdn-imager-x', 'Purge Images');
    }

    static function id(): string
    {
        return 'purge-images';
    }

    public static function iconPath(): ?string
    {
        return null;
    }

    /**
     * @throws SyntaxError
     * @throws Exception
     * @throws RuntimeError
     * @throws LoaderError
     */
    static function contentHtml(): string
    {
        Craft::$app->getView()->registerJs("new Craft.GlueCdn();");

        // Register asset bundle
        try {
            Craft::$app->getView()->registerAssetBundle(PurgeImagesAssets::class);
        } catch (InvalidConfigException) {
            return Craft::t('glue-cdn-imager-x', 'Could not load asset bundle');
        }

        // Render template
        return Craft::$app->getView()->renderTemplate(
            'glue-cdn-imager-x/utility/_utility'
        );
    }
}
