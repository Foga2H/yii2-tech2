<?php

namespace backend\controllers;

use common\models\User;
use yii\rest\Controller;

class ProfileController extends Controller
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
     * @return User|null
     */
    public function actionIndex() {
        return $this->findModel();
    }

    /**
     * @return array
     */
    public function verbs()
    {
        return [
            'index' => ['get'],
        ];
    }

    /**
     * @return User|null
     */
    private function findModel()
    {
        return User::findOne(\Yii::$app->user->id);
    }
}