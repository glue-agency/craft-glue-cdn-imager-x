<?php

namespace GlueAgency\CDN\controllers;

use Craft;
use craft\web\Controller;
use GlueAgency\CDN\GlueTransformer;
use GlueAgency\CDN\helpers\GlueCdnHelpers;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Images controller
 */
class ImagesController extends Controller
{
    /**
     * glue-cdn-imager-x/images action
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionPurge(): Response
    {
        $this->requireAcceptsJson();
        $this->requirePostRequest();

        if (!Craft::$app->request->getRequiredBodyParam('asset_urls')) {
            throw new NotFoundHttpException('No asset ids', 404);
        }

        $asset_urls = Craft::$app->request->getBodyParam('asset_urls');

        $returnData = [
            'success' => false,
            'message' => 'Something went wrong',
        ];


        if (GlueTransformer::$plugin->glueCdnService->purgeCdnImages($asset_urls)) {
            $returnData = [
                'success' => true,
                'message' => 'Urls succesfully purged',
            ];
        }

        return $this->asJson($returnData);
    }
}
