<?php

namespace backend\controllers;

use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

class BaseRestController extends Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['authMethods'] = [
            HttpBasicAuth::class,
            HttpBearerAuth::class
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@']
                ],
            ],
        ];

        return $behaviors;
    }

    /**
     * @param string $id
     * @param array $params
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidRouteException
     */
    public function runAction($id, $params = [])
    {
        $params = \yii\helpers\BaseArrayHelper::merge(\Yii::$app->getRequest()->getBodyParams(), $params);
        return parent::runAction($id, $params);
    }

}