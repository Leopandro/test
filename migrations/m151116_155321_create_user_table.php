<?php

use yii\db\Schema;
use yii\db\Migration;

class m151116_155321_create_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'name' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(255),
            'status' => $this->integer()->notNull(),
            'secret_key' => $this->string(255),
        ]);
        $this->createIndex('i_username', 'user', 'username', 'true');
        $this->createIndex('i_email', 'user', 'email', 'true');
        $this->createTable('login', [
            'id' => $this->primaryKey()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'ip' => $this->string(255)->notNull()
        ]);
        $this->addForeignKey('fk_user_id', 'login', 'user_id', 'user', 'id');
    }

    public function safeDown()
    {
        echo "m151116_155321_create_user_table cannot be reverted.\n";

        $this->dropTable('user');
        $this->dropTable('login');
        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
