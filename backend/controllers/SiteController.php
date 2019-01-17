<?php
namespace backend\controllers;

use backend\models\SignupForm;
use common\models\User;
use Yii;
use yii\rest\Controller;
use backend\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function actionIndex()
    {
        return 'api';
    }

    /**
     * @return array|LoginForm
     * @throws \yii\base\Exception
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->bodyParams, '');

        if ($token = $model->auth()) {
            return [
                'token' => $token->token,
                'expired' => date(DATE_RFC3339, $token->expired_at)
            ];
        }

        return $model;
    }

    /**
     * @return array|SignupForm
     * @throws \yii\base\Exception
     */
    public function actionRegister()
    {
        $model = new SignupForm();
        $model->load(Yii::$app->request->bodyParams, '');

        if ($user = $model->signup()) {
            $model->afterRegister($user);

            return [
                'success' => true,
            ];
        }

        return $model;
    }

    protected function verbs()
    {
        return [
            'login' => ['post'],
        ];
    }
}
