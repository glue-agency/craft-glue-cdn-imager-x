<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace GlueAgency\CDN\services;

use Craft;
use GlueAgency\CDN\GlueTransformer;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use yii\base\Component;

/**
 * Glue Cdn Service
 */
class GlueCdnService extends Component
{
    /**
     * @throws GuzzleException
     */
    public function purgeCdnImages(string $asset_urls): bool
    {
        $glueCdnConfig = GlueTransformer::getInstance()->getSettings();

        $payload = [
            'asset_urls' => explode(',', $asset_urls)
        ];

        try {
            $client = Craft::createGuzzleClient([
                'base_uri' => $glueCdnConfig->baseUrl,
                'verify' => getenv('CRAFT_ENVIRONMENT') === 'production',
                'headers'  => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . getenv('GLUE_CDN_API_TOKEN'),
                ],
                'json'     => $payload
            ]);

            $client->post('/api/purge');
        } catch (ClientException $e) {
            return $e->getCode();
        }

        return true;
    }
}
