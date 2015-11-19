<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $name
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property integer $status
 * @property connection[] $connections
 * @property string $secret_key
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_UNCONFIRMED_EMAIL = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'name', 'email', 'password_hash', 'auth_key'], 'string', 'max' => 255],
            [['status'], 'integer'],
            [['username'], 'unique'],
            [['email'], 'unique'],
            ['secret_key', 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'name' => 'Name',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'auth_key' => 'Auth Key',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConnections()
    {
        return $this->hasMany(Connection::className(), ['user_id' => 'id']);
    }

    /*
     * Проверка времени действия секретного ключа
     */

    public static function isSecretKeyExpire($key)
    {
        if (empty($key))
            return false;
        $expire = Yii::$app->params['secretKeyExpire'];
        $parts = explode('_', $key);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }


    /*
     *  Поиск по секретному ключу
     */

    public static function findBySecretKey($key)
    {
        if (!static::isSecretKeyExpire($key))
        {
            return null;
        }
        return static::findOne([
            'secret_key' => $key
        ]);
    }
    /*
     *
     */
    public function generateSecretKey()
    {
        $this->secret_key = Yii::$app->security->generateRandomString().'_'.time();
    }
    /*
     *
     */

    public function removeSecretKey()
    {
        $this->secret_key = null;
    }
    /*
    * Аутентификация пользователя
    */

    /*
     * Поиск по имени пользователя
     */

    public static function findByUsername($username)
    {
        return static::findOne([
            'username' => $username
        ]);
    }

    /*
     * Установка пароля
     */

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /*
     * Валидация пароля
     */

    public function validatePassword($password)
    {

        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /*
     * Поиск пользователя по id c активным статусом
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /*
     * Поиск соответствия по ключу доступа
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Возвращает id этого пользователя
     */
    public function getId()
    {
        return $this->id;
    }

    /*
     * Генерирует рандомную строку для использования в валидации
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Возвращает ключ используемый для валидации через cookie
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Сравнивает ключ полученный от cookie клиента с ключом на сервере
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
}
