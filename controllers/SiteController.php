<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\RegForm;
use Yii;
use app\models\Connection;
use app\models\SendEmailForm;
use app\models\ResetPasswordForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

class SiteController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest)
        {
            $connections = Connection::find()
                ->where(['user_id' => Yii::$app->user->identity->id])
                ->orderBy(['id' => SORT_DESC])
                ->all();
            return $this->render('index', [
                'connections' => $connections
            ]);
        }
        return $this->render('index');
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login())
        {
            return $this->goHome();
        }
        return $this->render('login', [
            'model' => $model
        ]);
    }

    public function actionReset($key)
    {
        try
        {
            $model = new ResetPasswordForm($key);
        }
        catch(InvalidParamException $e)
        {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->resetPassword())
            {
                Yii::$app->getSession()->setFlash('warning', 'Пароль изменен.');
            }
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
    public function actionEmail()
    {
        $model = new SendEmailForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                // form inputs are valid, do something here
                if ($model->sendEmail())
                {
                    Yii::$app->getSession()->setFlash('warning', 'Проверьте email');
                    return $this->goHome();
                }
            }
        }

        return $this->render('sendEmail', [
            'model' => $model,
        ]);
    }

    public function actionReg()
    {
        $model = new RegForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            if ($user = $model->reg())
            {
                if (Yii::$app->getUser()->login($user))
                {

                    return $this->goHome();
                }
            }
        }
        return $this->render('reg', [
            'model' => $model
        ]);
    }
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


}
