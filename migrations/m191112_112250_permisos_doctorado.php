<?php

use yii\db\Migration;

/**
 * Class m191112_112250_permisos_doctorado
 */
class m191112_112250_permisos_doctorado extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $escuelaDoctoradoRole = $auth->getRole('escuelaDoctorado');

        $editarPlanPermission = $auth->getPermission('editarPlan');
        $auth->addChild($escuelaDoctoradoRole, $editarPlanPermission);

        $editarInformePermission = $auth->getPermission('editarInforme');
        $auth->addChild($escuelaDoctoradoRole, $editarInformePermission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191112_112250_permisos_doctorado cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191112_112250_permisos_doctorado cannot be reverted.\n";

        return false;
    }
    */
}
