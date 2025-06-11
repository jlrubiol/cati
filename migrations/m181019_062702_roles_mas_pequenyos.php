<?php

use yii\db\Migration;

/**
 * Class m181019_062702_roles_mas_pequenyos
 */
class m181019_062702_roles_mas_pequenyos extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Ya existía un rol `unidadCalidad` que tenía concedidos todos los permisos de gestión.
        // Lo buscamos y renombramos a `gestor`.  Habrá que cambiar el nombre en el código.
        $gestorRole = $auth->getRole('unidadCalidad');
        $gestorRole->name = 'gestor';
        $gestorRole->description = 'Usuarios con permisos de gestión';
        $auth->update('unidadCalidad', $gestorRole);

        // Crear el rol "unidadCalidad" y darle los permisos del rol Gestor.
        $unidadCalidadRole = $auth->createRole('unidadCalidad');
        $unidadCalidadRole->description = 'Gestores de la Unidad de Calidad y Racionalización';
        $auth->add($unidadCalidadRole);
        $auth->addChild($unidadCalidadRole, $gestorRole);

        // Crear el rol "gradoMaster" y darle los permisos del rol Gestor.
        $gradoMasterRole = $auth->createRole('gradoMaster');
        $gradoMasterRole->description = 'Gestores de la Sección de Grado y Máster';
        $auth->add($gradoMasterRole);
        $auth->addChild($gradoMasterRole, $gestorRole);

        // Crear el rol "escuelaDoctorado" y darle los permisos del rol Gestor.
        $escuelaDoctoradoRole = $auth->createRole('escuelaDoctorado');
        $escuelaDoctoradoRole->description = 'Gestores de la Escuela de Doctorado';
        $auth->add($escuelaDoctoradoRole);
        $auth->addChild($escuelaDoctoradoRole, $gestorRole);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181019_062702_roles_mas_pequenyos cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181019_062702_roles_mas_pequenyos cannot be reverted.\n";

        return false;
    }
    */
}
