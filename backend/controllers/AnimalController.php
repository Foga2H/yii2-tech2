<?php

namespace backend\controllers;

use common\models\Animal;
use common\models\AnimalTypes;
use common\models\User;
use yii\base\DynamicModel;
use yii\base\ErrorException;

class AnimalController extends BaseRestController
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
        return $this->getAnimals();
    }

    /**
     * @return array
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUseHearts()
    {
        return $this->useHearts();
    }

    /**
     * @param null $animal_id
     * @return Animal[]
     * @throws ErrorException
     */
    public function actionGetParents($animal_id = null)
    {
        return $this->getParents($animal_id);
    }

    /**
     * @param null $animal_id
     * @return Animal[]
     * @throws ErrorException
     */
    public function actionGetChilds($animal_id = null)
    {
        return $this->getChilds($animal_id);
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    private function getAnimals()
    {
        $animal = Animal::find();

        if ($type = \Yii::$app->getRequest()->getQueryParam('animal_type')) {
            $model = DynamicModel::validateData(['animal_type' => $type], [
                ['animal_type', 'integer', 'min' => 0],
            ]);

            if ($model->validate()) {
                if ($animalType = AnimalTypes::findOne(['id' => $type])) {
                    $animal->where(['animal_type_id' => $type]);
                } else {
                    throw new ErrorException('Animal type not found');
                }
            } else {
                throw new ErrorException('Type must be a integer');
            }
        }

        if ($ageFrom = \Yii::$app->getRequest()->getQueryParam('ageFrom') &&
            $ageTo = \Yii::$app->getRequest()->getQueryParam('ageTo')) {

            $model = DynamicModel::validateData(['ageFrom' => $ageFrom, 'ageTo' => $ageTo], [
                [['ageFrom', 'ageTo'], 'integer', 'min' => 0],
            ]);

            if ($model->validate()) {
                $animal->where(['>=', 'age', (int) $ageFrom])->andWhere(['<=', 'age', (int) $ageTo]);
            } else {
                throw new ErrorException('Age must be a integer');
            }
        }

        return $animal->all();
    }

    /**
     * @return array
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    private function useHearts()
    {
        $animal_id = \Yii::$app->getRequest()->getBodyParam('animal_id');
        $hearts = \Yii::$app->getRequest()->getBodyParam('hearts');

        if (!is_null($animal_id) && !is_null($hearts)) {

            $model = DynamicModel::validateData(['animal_id' => $animal_id, 'hearts' => $hearts], [
                [['animal_id', 'hearts'], 'integer', 'min' => 1],
            ]);

            if ($model->validate()) {
                if ($animal = Animal::findOne(['id' => $animal_id])) {
                    if ($animal->user_id === \Yii::$app->user->id) {
                        $user = User::findOne(['id' => \Yii::$app->user->id]);

                        if ($user->getHearts() >= $hearts) {

                            if ($animal = Animal::addHearts($animal_id, $hearts)) {
                                User::setHearts(\Yii::$app->user->id, $user->getHearts() - $hearts);

                                return [
                                    'success' => true,
                                ];
                            }

                            throw new ErrorException('Animal not found');
                        }

                        throw new ErrorException('You havent enough hearts');
                    }

                    throw new ErrorException('Animal is not yours');
                }

                throw new ErrorException('Animal not found');
            }

            throw new ErrorException('Animal ID / Hearts must be a integer an more than 0');
        }

        throw new ErrorException('Fields animal_id, hearts is required');
    }

    /**
     * @param $animal_id
     * @return Animal[]
     * @throws ErrorException
     */
    private function getParents($animal_id)
    {
        if ($animal = Animal::findOne(['id' => $animal_id])) {

            if ($parents = Animal::findParents($animal)) {
                return Animal::findAll($parents);
            }

            throw new ErrorException('Parents not found');
        }

        throw new ErrorException('Animal not found');
    }

    /**
     * @param $animal_id
     * @return Animal[]
     * @throws ErrorException
     */
    private function getChilds($animal_id)
    {
        if ($animal = Animal::findOne(['id' => $animal_id])) {

            if ($childs = Animal::findChilds($animal)) {
                return Animal::findAll($childs);
            }

            throw new ErrorException('Childs not found');
        }

        throw new ErrorException('Animal not found');
    }

    /**
     * @return array
     */
    protected function verbs()
    {
        return [
            'index' => ['get'],
            'use-hearts' => ['post'],
            'get-parents' => ['get'],
            'get-childs' => ['get']
        ];
    }
}