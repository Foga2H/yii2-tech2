<?php

namespace backend\controllers;

use common\models\User;
use yii\base\DynamicModel;
use yii\base\ErrorException;
use yii\db\Query;

class UserController extends BaseRestController
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
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        return $this->getUsers();
    }

    /**
     * @param null $user_id
     * @return User|null
     * @throws ErrorException
     */
    public function actionUser($user_id = null)
    {
        return $this->getUser($user_id);
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    private function getUsers()
    {
        $users = User::find();

        $animalCount = \Yii::$app->getRequest()->getQueryParam('animalCount');
        $animalType = \Yii::$app->getRequest()->getQueryParam('animalType');

        if (!is_null($animalCount) && !is_null($animalType)) {
            $model = DynamicModel::validateData(['animalCount' => $animalCount, 'animalType' => $animalType], [
                [['animalCount', 'animalType'], 'integer', 'min' => 0],
            ]);

            if ($model->validate()) {
                if ($users = User::byAnimalCountAndType($animalCount, $animalType)) {
                    return $users;
                }

                throw new ErrorException('Users not found');
            } else {
                throw new ErrorException('Animal count / type must be a integer');
            }
        } else if (!is_null($animalCount)) {
            $model = DynamicModel::validateData(['animalCount' => $animalCount], [
                [['animalCount'], 'integer', 'min' => 0],
            ]);

            if ($model->validate()) {
                if ($users = User::byAnimalCount($animalCount)) {
                    return $users;
                }

                throw new ErrorException('Users not found');
            } else {
                throw new ErrorException('Animal count must be a integer');
            }
        }

        return $users->all();
    }

    /**
     * @param null $user_id
     * @return User|null
     * @throws ErrorException
     */
    private function getUser($user_id = null)
    {
        if ($user = User::findOne($user_id)) {
            return $user;
        }

        throw new ErrorException('User not found');
    }

    /**
     * @return array
     */
    protected function verbs()
    {
        return [
            'index' => ['get'],
            'user' => ['get']
        ];
    }
}