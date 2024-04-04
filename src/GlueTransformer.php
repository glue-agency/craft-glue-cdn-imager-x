<?php

namespace GlueAgency\CDN;

use Craft;
use craft\base\Element;
use craft\base\Model;
use craft\base\Plugin;
use craft\elements\Asset;
use craft\events\DefineHtmlEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterElementActionsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\Html;
use craft\services\Utilities;
use craft\web\UrlManager;
use GlueAgency\CDN\assetbundles\PurgeImagesAssets;
use GlueAgency\CDN\elements\actions\PurgeImagesElementAction;
use GlueAgency\CDN\models\Settings;
use GlueAgency\CDN\services\GlueCdnService;
use GlueAgency\CDN\transformers\GlueCDN;
use GlueAgency\CDN\utilities\PurgeImages;
use spacecatninja\imagerx\events\RegisterTransformersEvent;
use spacecatninja\imagerx\ImagerX;
use yii\base\Event;
use yii\base\InvalidConfigException;

/**
 * Glue CDN for Imager X plugin
 *
 * @method static GlueTransformer getInstance()
 * @property-read GlueCdnService $glueCdnService
 * @author    Glue Agency <support@glue.be>
 * @copyright Glue Agency
 * @license   MIT
 */
class GlueTransformer extends Plugin
{
    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * ImagerX::$plugin
     *
     * @var GlueTransformer
     */
    public static GlueTransformer $plugin;

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        Craft::setAlias('@glue-cdn', $this->getBasePath());

        // Register transformer with Imager
        Event::on(ImagerX::class,
            ImagerX::EVENT_REGISTER_TRANSFORMERS,
            static function (RegisterTransformersEvent $event) {
                $event->transformers['glue-cdn'] = GlueCDN::class;
            }
        );

        Event::on(Utilities::class, Utilities::EVENT_REGISTER_UTILITY_TYPES, function (RegisterComponentTypesEvent $event) {
            if(!getenv('GLUE_CDN_API_TOKEN')) {
                return;
            }
            $event->types[] = PurgeImages::class;
        });

        // Register element action to assets for clearing transforms
        Event::on(Asset::class, Element::EVENT_REGISTER_ACTIONS,
            static function(RegisterElementActionsEvent $event) {
                if(!getenv('GLUE_CDN_API_TOKEN')) {
                    return;
                }
                $event->actions[] = PurgeImagesElementAction::class;
            }
        );

        Event::on(
            Asset::class,
            Element::EVENT_DEFINE_ADDITIONAL_BUTTONS,
            function (DefineHtmlEvent $event) {
                /** @var Asset $asset */
                $asset = $event->sender;

                if(!getenv('GLUE_CDN_API_TOKEN') || $asset->kind !== Asset::KIND_IMAGE) {
                    return;
                }
                $assetUrl = $asset->getUrl();
                $html = Html::button(Craft::t('app', 'Purge CDN'), [
                    'id' => 'purge-single-btn',
                    'class' => 'btn',
                    'data' => [
                        'icon' => 'trash',
                        'asset-url' => $assetUrl
                    ],
                    'aria' => [
                        'label' => Craft::t('app', 'Purge CDN'),
                    ],
                ]);

                try {
                    Craft::$app->getView()->registerAssetBundle(PurgeImagesAssets::class);
                } catch (InvalidConfigException) {
                    return Craft::t('glue-cdn-imager-x', 'Could not load asset bundle');
                }

                Craft::$app->getView()->registerJs("new Craft.GlueCdn();");

                $event->html .= $html;
            }
        );

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            $this->_registerCpRoutes();
        }
    }

    public static function config(): array
    {
        return [
            'components' => [
                'glueCdnService' => GlueCdnService::class,
            ],
        ];
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings;
    }

    private function _registerCpRoutes(): void
    {
        // Register our CP routes
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, [
                'glue-cdn-imager-x/images/purge' => 'glue-cdn-imager-x/images/purge',
            ]);
        });
    }
}
