<?php

/**
 * Crea la jerarquía de permisos para editar la información de los estudios.
 *
 * Ejecutar con `yii editor-informacion/init` (después de editor-informe).
 * Puede ser necesario refrescar las rutas en Administración -> Permisos
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\rbac\DbManager;

class EditorInformacionController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager instanceof DbManager ? Yii::$app->authManager : new DbManager();

        // Crear y añadir el permiso `editarInformacion`.
        $editarInformacion = $auth->createPermission('editarInformacion');
        $editarInformacion->description = 'Editar las informaciones';
        $auth->add($editarInformacion);

        // El rol `unidadCalidad` está definido en
        //     /commands/EditorInformeController.php
        // Buscarlo y darle el permiso `editarInformacion`
        // para que pueda editar la información de cualquier estudio.
        $unidadCalidad = $auth->getRole('unidadCalidad');
        $auth->addChild($unidadCalidad, $editarInformacion);

        // Buscar la regla para comprobar si el usuario es
        // coordinador o delegado de un plan del estudio dado
        // La regla fue añadida en `/commands/EditorInformeController.php`.
        $rule = $auth->getRule('esCoordinadorDelEstudio');

        // Añadir el permiso `editarInformacionPropia` y asociarle la regla
        // para que los coordinadores y delegados puedan editar sus informaciones.
        $editarInformacionPropia = $auth->createPermission('editarInformacionPropia');
        $editarInformacionPropia->description = 'Editar informacion del plan del que se es coordinador o delegado';
        $editarInformacionPropia->ruleName = $rule->name;
        $auth->add($editarInformacionPropia);

        // Darle al permiso `editarInformacionPropia` el subpermiso `editarInformacion`.
        $auth->addChild($editarInformacionPropia, $editarInformacion);

        // El rol `coordinadorPlan` está definido en
        //     /commands/EditorInformeController.php
        $coordinadorPlan = $auth->getRole('coordinadorPlan');

        // Darle al rol `coordinadorPlan` el permiso `editarInformacionPropia`
        // para permitir editar la información de sus estudios.
        $auth->addChild($coordinadorPlan, $editarInformacionPropia);
    }
}
