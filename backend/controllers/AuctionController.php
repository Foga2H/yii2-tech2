<?php

namespace backend\controllers;

use common\models\Auction;
use yii\base\UserException;

class AuctionController extends BaseRestController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return $behaviors;
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        return $this->getAuctions();
    }

    /**
     * @param $animal_id
     * @param $price
     * @return array
     * @throws \yii\base\ErrorException
     */
    public function actionAddItem($animal_id, $price)
    {
        if ($item = Auction::createItem($animal_id, $price)) {
            return [
                'success' => true,
            ];
        }
    }

    /**
     * @param $item_id
     * @return array|bool|\common\models\Animal|null
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionBuyItem($item_id)
    {
        if ($item = Auction::buyItem($item_id)) {
            return [
                'success' => true,
            ];
        }
    }

    /**
     * @return array
     */
    public function verbs()
    {
        return [
            'index' => ['get'],
            'add-item' => ['post'],
            'buy-item' => ['post'],
        ];
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    private function getAuctions()
    {
        return Auction::find()->all();
    }


}