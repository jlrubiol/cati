<?php

/**
 * Crea la jerarquía de permisos para ver los extractos de los PAIMs de un centro.
 *
 * Ejecutar con `yii extractos-paim-centro/init`.
 * Puede ser necesario refrescar las rutas en Administración -> Permisos
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\rbac\DbManager;

class ExtractosPaimCentroController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager instanceof DbManager ?
          Yii::$app->authManager : new DbManager();

        // Crear y añadir el permiso `verExtractosPaimCentro`.
        $verExtractosPaimCentro = $auth->createPermission('verExtractosPaimCentro');
        $verExtractosPaimCentro->description = 'Ver los extractos de los PAIMs de los centros';
        $auth->add($verExtractosPaimCentro);

        // El rol `unidadCalidad` está definido en
        //     /commands/EditorInformeController.php
        // Buscarlo y darle el permiso `verExtractosPaimCentro`
        // para que pueda ver los extractos de los PAIM de cualquier centro.
        $unidadCalidad = $auth->getRole('unidadCalidad');
        $auth->addChild($unidadCalidad, $verExtractosPaimCentro);

        // Añadir la regla `esDecanodelCentro` para comprobar si el usuario es
        // decano o director de un centro dado.
        $rule = new \app\rbac\DecanoCentroRule();
        $auth->add($rule);

        // Crear el permiso `verExtractosPaimCentroPropio` y asociarle la regla anterior
        // para que los decanos y directores ver los extractos de su centro.
        $verExtractosPaimCentroPropio = $auth->createPermission('verExtractosPaimCentroPropio');
        $verExtractosPaimCentroPropio->description = 'Ver los extractos de los PAIMs del centro del que se es decano o director';
        $verExtractosPaimCentroPropio->ruleName = $rule->name;
        $auth->add($verExtractosPaimCentroPropio);

        // Darle al permiso `verExtractosPaimCentroPropio` el subpermiso `verExtractosPaimCentro`.
        $auth->addChild($verExtractosPaimCentroPropio, $verExtractosPaimCentro);

        // Crear el rol `decanoCentro`.
        // IMPORTANTE: También hay que añadirlo como `defaultRole` en `config/web.php`
        // para que cuando se use `$user->can('verExtractosPaimCentro')` se le de al usuario
        // este rol si la regla devuelve `true`.
        $decanoCentro = $auth->createRole('decanoCentro');
        $decanoCentro->description = 'Decano o director de un centro';
        // Si la regla devuelve `true`, se aplica el rol al usuario actual.
        // Tiene que estar definido como `defaultRole` en el fichero de configuración.
        $decanoCentro->ruleName = $rule->name;
        $auth->add($decanoCentro);

        // Darle al rol `decanoCentro` el permiso `editarInformacionPropia`
        // para permitirle ver los extractos de los PAIMs de su centro.
        $auth->addChild($decanoCentro, $verExtractosPaimCentroPropio);
    }
}
