<?php

namespace app\controllers;

use Yii;
use app\models\Calendario;
use app\models\NotasPlan;
use app\models\Plan;

/**
* This is the class for controller "NotasPlanController".
*/
class NotasPlanController extends \app\controllers\base\NotasPlanController
{
    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'rules' => [
                    [
                        'actions' => ['editar', 'guardar'],
                        'allow' => true,
                        'roles' => ['gradoMaster'],
                    ], /*[
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['Admin'],
                    ],*/
                ],
            ],
        ]);
    }

    /** Muestra el formulario para editar las notas de un plan de estudios */
    public function actionEditar($plan_id_nk)
    {
        $notas = NotasPlan::getNotasPlan($plan_id_nk);
        $plan = Plan::getPlanByNk(Calendario::getAnyoAcademico(), $plan_id_nk);

        return $this->render('editar', [
            'notas' => $notas,
            'plan' => $plan,
        ]);
    }

    /** Guarda las notas de un plan de estudios en la tabla notasPlan_lang */
    public function actionGuardar()
    {
        $request = Yii::$app->request;
        $language = Yii::$app->language;

        $notas = NotasPlan::findOne(['id' => $request->post('notas_id')]);

        if (!$notas) {
            $notas = new NotasPlan(['plan_id_nk' => $request->post('plan_id_nk')]);
        }

        $notas->language = $language;
        $notas->texto = $request->post('texto');
        if ($notas->save()) {
            $nombre_usuario = Yii::$app->user->identity->username;
            Yii::info("{$nombre_usuario} ha guardado las notas del plan {$notas->plan_id_nk}", 'gestion');
        }

        return $this->redirect([
            'gestion/lista-notas-planes',
        ]);
    }
}
