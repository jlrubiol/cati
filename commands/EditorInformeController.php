<?php
/**
 * Crea la jerarquía de permisos para editar los informes de evaluación.
 *
 * Ejecutar con `yii editor-informe/init`.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\rbac\DbManager;

class EditorInformeController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager instanceof DbManager ? Yii::$app->authManager : new DbManager();

        // Crear y añadir el permiso `editarInforme`.
        $editarInforme = $auth->createPermission('editarInforme');
        $editarInforme->description = 'Editar los informes';
        $auth->add($editarInforme);

        // Crear y añadir el rol `unidadCalidad`.
        $unidadCalidad = $auth->createRole('unidadCalidad');
        $unidadCalidad->description = 'Unidad de Calidad y Racionalización';
        $auth->add($unidadCalidad);
        // Darle al rol `unidadCalidad` el permiso `editarInforme`.
        $auth->addChild($unidadCalidad, $editarInforme);

        // Crear y añadir el permiso `gestionarCalidad`.
        $gestionarCalidad = $auth->createPermission('gestionarCalidad');
        $gestionarCalidad->description = 'Gestionar el sitio web de Calidad de las Titulaciones';
        $auth->add($gestionarCalidad);
        // Darle al rol `unidadCalidad` el permiso `gestionarCalidad`.
        $auth->addChild($unidadCalidad, $gestionarCalidad);

        // Darle al rol `Admin` los permisos del rol `unidadCalidad`.
        $admin = $auth->getRole('Admin');
        $auth->addChild($admin, $unidadCalidad);

        // Añadir una regla para comprobar si el usuario es coordinador o delegado
        // de un plan del estudio indicado.
        $rule = new \app\rbac\CoordinadorEstudioRule();
        $auth->add($rule);

        // Crear el permiso `editarInformePropio`, asociarle la regla anterior, y añadirlo.
        $editarInformePropio = $auth->createPermission('editarInformePropio');
        $editarInformePropio->description = 'Editar informe de un plan del que se es coordinador o delegado';
        $editarInformePropio->ruleName = $rule->name;
        $auth->add($editarInformePropio);

        // Darle al permiso `editarInformePropio` el subpermiso `editarInforme`.
        $auth->addChild($editarInformePropio, $editarInforme);

        // Crear el rol `coordinadorPlan`.
        $coordinadorPlan = $auth->createRole('coordinadorPlan');
        $coordinadorPlan->description = 'Coordinador de un plan';
        // Si la regla devuelve `true`, se aplica el rol al usuario actual.
        // Tiene que estar definido como `defaultRole` en el fichero de configuración.
        $coordinadorPlan->ruleName = $rule->name;
        $auth->add($coordinadorPlan);

        // Darle al rol `coordinadorPlan` el permiso `editarInformePropio` para que
        // los coordinadores puedan editar los informes de los estudios que coordinan.
        $auth->addChild($coordinadorPlan, $editarInformePropio);
    }
}
