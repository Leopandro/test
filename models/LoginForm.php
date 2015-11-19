<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 17.11.2015
 * Time: 17:00
 */

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $status;
    public $rememberMe;
    private $_user = false;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['username', 'validateUser'],
            ['password', 'validatePassword']
        ];
    }

    /*
     * Имена полей форм
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'password' => 'Пароль'
        ];
    }

    /*
     * Проверка пароля
     */

    public function validatePassword($attribute)
    {
        if (!$this->hasErrors())
        {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password))
            {
                $this->addError($attribute, 'Вы ввели неправильный пароль');
            }
        }
    }

    /*
     * Проверка существует ли пользователь
     */

    public function validateUser($attribute)
    {
        if (!$user = $this->getUser())
        {
            $this->addError($attribute, 'Такого пользователя не существует');
        }
    }

    /*
     * Получение экземпляра пользователя
     */
    public function getUser()
    {
        if ($this->_user == false)
        {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }

    public function login()
    {
        if ($this->validate())
        {
            $this->status = ($user = $this->getUser()) ? $user->status : User::STATUS_INACTIVE;
            if ($this->status == User::STATUS_ACTIVE)
            {
                $connection = new Connection();
                $connection->user_id = $this->getUser()->getId();
                $connection->ip = Yii::$app->request->getUserIP();
                $connection->save();
                return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
            }
        }
    }

}