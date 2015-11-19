<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 18.11.2015
 * Time: 19:38
 */

namespace app\models;

use Yii;
use yii\base\InvalidParamException;
use yii\base\model;

class ResetPasswordForm extends Model
{
    public $password;
    private $_user;

    public function rules()
    {
        return [
            ['password', 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль'
        ];
    }

    public function resetPassword()
    {
        /*
         * @var $user User
         */
        $user=$this->_user;
        $user->setPassword($this->password);
        $user->removeSecretKey();
        return $user->save();
    }

    public function __construct($key, $config = [])
    {
        if (empty($key) || !is_string($key))
        {
            throw new Exception('Ключ не может быть пустым');
        }
        $this->_user = User::findBySecretKey($key);
        if (!$this->_user)
        {
            throw new InvalidParamException('Не верный ключ');
        }
        parent::__construct($config);
    }
}