<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 18.11.2015
 * Time: 1:56
 */

namespace app\models;

use Yii;
use yii\base\Model;

class SendEmailForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => User::ClassName(),
                'filter' => [
                    'status' => User::STATUS_ACTIVE
                ],
                'message' => 'Данный email не зарегестрирован'
            ]
        ];
    }
    public function attributeLabels()
    {
        return [
            'email' => 'Почта пользователя'
        ];
    }
	
	public function sendEmail()
	{
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
		if ($user)
        {
            $user->generateSecretKey();
            if($user->save())
            {
                return Yii::$app->mailer->compose('resetPassword', ['user' => $user])
                    ->setForm([Yii::$app->params['supportEmail'] => Yii::$app->name.'Отправлено роботом'])
                    ->setTo($this->email)
                    ->setSubject('Сброс пароля для'.Yii::$app->name)
                    ->send();
            }
        }
	}
}