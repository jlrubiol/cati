<?php

use yii\db\Migration;

/**
 * Class m180301_122537_insert_default_superadmin.
 */
class m180301_122537_insert_default_superadmin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create default administrator "superadmin".
        // Password: superadmin.  Change it!!
        // Login URL: //user/login
        $time_now = time();

        $this->insert('user', [
            'username' => 'superadmin',
            'email' => 'admin@example.com',
            'password_hash' => '$2y$10$qFM5U37mmbFG7/Q/IHE/6OxD.O5FXVlaOo4K7JtltFSBRPcN1diaq',
            'auth_key' => '4g6V6_sg6FAB9wTgiZtrb-bTNqq0YXqF',
            'registration_ip' => null,
            'flags' => 0,
            'confirmed_at' => $time_now,
            'updated_at' => $time_now,
            'created_at' => $time_now,
        ]);

        $this->insert('profile', [
            'name' => 'Bastard Operator From Hell',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // username is unique index
        $this->delete('user', [
            'username' => 'superadmin',
        ]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180301_122537_insert_default_superadmin cannot be reverted.\n";

        return false;
    }
    */
}
