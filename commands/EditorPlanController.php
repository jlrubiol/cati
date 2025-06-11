<?php
/**
 * Crea la jerarquía de permisos para editar los planes de mejora.
 *
 * Ejecutar con `yii editor-plan/init` (después de haber ejecutado editor-informe).
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

class EditorPlanController extends Controller
{
    /**
     * Inicializa los permisos.
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager instanceof DbManager ?
          Yii::$app->authManager : new DbManager();

        // Crear y añadir el permiso `editarPlan`.
        $editarPlan = $auth->createPermission('editarPlan');
        $editarPlan->description = 'Editar los planes de innovación y mejora';
        $auth->add($editarPlan);

        // El rol `unidadCalidad` está definido en
        //     /commands/EditorInformeController.php
        // Buscarlo y darle el permiso `editarPlan`
        // para que pueda editar cualquier plan de innovación y mejora.
        $unidadCalidadRole = $auth->getRole('unidadCalidad');
        $auth->addChild($unidadCalidadRole, $editarPlan);

        // Añadir una regla para comprobar si el usuario es
        // * coordinador o delegado de un plan de este estudio
        // * presidente de una CGC de un plan de este estudio
        $rule = new \app\rbac\PresidenteEstudioRule();
        $auth->add($rule);
        $rule = new \app\rbac\CoorOPresiEstudioRule();
        $auth->add($rule);

        // Crear el permiso `editarPlanPropio` y asociarle la regla `CoorOPresiEstudio`
        // para que los coordinadores, delegados y presidentes puedan editar sus planes.
        $editarPlanPropio = $auth->createPermission('editarPlanPropio');
        $editarPlanPropio->description = 'Editar plan de mejora de un plan del
            que se es coordinador o presidente';
        $editarPlanPropio->ruleName = $rule->name;
        $auth->add($editarPlanPropio);

        // Darle al permiso `editarPlanPropio` el subpermiso `editarPlan`.
        $auth->addChild($editarPlanPropio, $editarPlan);

        // Crear el rol `coorOPresiPlan`.
        // IMPORTANTE: También hay que añadirlo como `defaultRole` en `config/web.php`
        // para que cuando se use `$user->can('editarPlan')` se le de al usuario
        // este rol si la regla devuelve `true`.
        $coorOPresiPlan = $auth->createRole('coorOPresiPlan');
        $coorOPresiPlan->description = 'Coordinador o Presidente de la Comisión
            de Garantía de un plan';
        $coorOPresiPlan->ruleName = $rule->name;
        $auth->add($coorOPresiPlan);

        // Darle al rol `coorOPresiPlan` el permiso `editarPlanPropio`
        // para permitirle editar sus propios planes de innovación y mejora.
        $auth->addChild($coorOPresiPlan, $editarPlanPropio);
    }
}
