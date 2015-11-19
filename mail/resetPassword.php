<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 18.11.2015
 * Time: 19:33
 * @var $user \app\models\User
 */

use yii\helpers\Html;

echo 'Привет '.Html::encode($user->username).'. ';
echo Html::a('Для смены пароля перейдите по ссылке.'.
    Yii::$app->urlManager->createAbsoluteUrl(
        [
            '/main/reset-password',
            'key' => $user->secret_key
        ]
    ));


