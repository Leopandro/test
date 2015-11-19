<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 17.11.2015
 * Time: 3:31
 */

namespace app\models;

use Yii;
use yii\base\Model;

class RegForm extends Model
{
    public $username;
    public $name;
    public $email;
    public $password;
    public $retype_password;

    public function rules()
    {
        return [
            [['username', 'name', 'email', 'password'], 'required'],
            [['username', 'email', 'password'], 'filter', 'filter' => 'trim'],
            ['username', 'unique', 'targetClass' => User::className(), 'message' => 'Это имя уже занято'],
            ['email', 'email', 'message' => 'Введите валидный email'],
            ['email', 'unique',  'targetClass' => User::className(), 'message' => 'Эта почта уже используется'],
            ['password', 'string', 'min' => 6, 'tooShort' => 'Слишком короткий пароль'],
            ['retype_password', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают']
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'email' => 'Эл. почта',
            'password' => 'Пароль'
        ];
    }

    public function reg()
    {
        $user = new User();
        $user->username = $this->username;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->status = 1;
        if ($user->save())
        {
            $connection = new Connection();
            $connection->user_id = $user->id;
            $connection->ip = Yii::$app->request->getUserIP();
            $connection->save();
            return $user;
        }
        else
            return null;
    }
}