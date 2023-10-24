<?php

namespace GlueAgency\CDN;

use craft\base\Model;
use craft\base\Plugin;
use GlueAgency\CDN\models\Settings;
use GlueAgency\CDN\transformers\GlueCDN;
use spacecatninja\imagerx\events\RegisterTransformersEvent;
use spacecatninja\imagerx\ImagerX;
use yii\base\Event;

/**
 * Glue CDN for Imager X plugin
 *
 * @method static GlueTransformer getInstance()
 * @author    Glue Agency <support@glue.be>
 * @copyright Glue Agency
 * @license   MIT
 */
class GlueTransformer extends Plugin
{

    public function init(): void
    {
        parent::init();

        // Register transformer with Imager
        Event::on(ImagerX::class,
            ImagerX::EVENT_REGISTER_TRANSFORMERS,
            static function(RegisterTransformersEvent $event) {
                $event->transformers['glue-cdn'] = GlueCDN::class;
            }
        );
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings;
    }
}
