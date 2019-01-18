<?php

namespace console\controllers;

use common\models\Animal;
use common\models\User;
use yii\console\Controller;

class GameController extends Controller
{
    const CHILDBIRTH_TIME = 7200;
    const BONUS_HEARTS_TIME = 1296000;

    /**
     * Try create a child for animals.
     */
    public function actionTryChildbirth()
    {
        foreach (User::find()->all() as $user) {
            Animal::tryChildbirthByUser($user);
        }
    }

    /**
     * @throws \yii\base\ErrorException
     */
    public function actionGiveBonusHearts()
    {
        foreach (User::find()->all() as $user) {
            User::giveHeartsByAnimalCount($user);
        }
    }

    /**
     * Substract age from animals. By 1 heart.
     */
    public function animalOldness()
    {
        foreach (Animal::find()->all() as $animal) {
            Animal::setHearts($animal->id, $animal->hearts - 1);
        }
    }
}