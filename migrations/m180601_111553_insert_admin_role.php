<?php

use yii\db\Migration;

/**
 * Class m180601_111553_insert_admin_role
 */
class m180601_111553_insert_admin_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Add "admin" role
        $admin = $auth->createRole('Admin');
        $admin->description = 'Usuarios administradores';
        $auth->add($admin);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180601_111553_insert_admin_role cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180601_111553_insert_admin_role cannot be reverted.\n";

        return false;
    }
    */
}
