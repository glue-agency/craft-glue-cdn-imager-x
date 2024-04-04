<?php

namespace GlueAgency\CDN\elements\actions;

use Craft;
use craft\base\ElementAction;
use craft\elements\Asset;
use craft\elements\db\ElementQueryInterface;
use Exception;
use GlueAgency\CDN\GlueTransformer;

/**
 * Purge Images element action
 */
class PurgeImagesElementAction extends ElementAction
{
    public static function displayName(): string
    {
        return Craft::t('glue-cdn-imager-x', 'Purge Images');
    }

    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('glue-cdn-imager-x', 'Purge CDN Images');
    }


    public function performAction(ElementQueryInterface $query): bool
    {
        try {
            $assetUrls = [];
            foreach ($query->kind(Asset::KIND_IMAGE)->all() as $asset) {
                $assetUrls[] = $asset->getUrl();
            }

            GlueTransformer::$plugin->glueCdnService->purgeCdnImages(implode(',', $assetUrls));

        } catch (Exception $exception) {
            $this->setMessage($exception->getMessage());
            return false;
        }

        $this->setMessage(Craft::t('glue-cdn-imager-x', 'Image transforms have been purged'));
        return true;
    }
}
