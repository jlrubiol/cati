<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use app\models\Doctorado;
use app\models\Estudio;

/**
* This is the class for controller "DoctoradoController".
*/
class DoctoradoController extends \app\controllers\base\DoctoradoController
{
    public function behaviors()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $anyo = $request->get('anyo');

        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['editar-datos'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) use ($id, $anyo) {
                            $doctorado = Doctorado::findOne(['id' => $id]);
                            if (!$doctorado) {
                                return false;
                            }
                            $estudio = Estudio::find()->where([
                                'id_nk' => $doctorado->cod_estudio,
                                'anyo_academico' => $anyo,
                            ])->one();

                            return Yii::$app->user->can('editarInforme', ['estudio' => $estudio]);
                        },
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->getResponse()->redirect(['//cati-auth/login']);
                    }
                    throw new ForbiddenHttpException(
                        Yii::t('app', 'No tiene permisos para acceder a esta página. 😨')
                    );
                },
            ],
        ];
    }

    /** Actualiza los datos de un programa de doctorado. */
    public function actionEditarDatos($id, $anyo)
    {
        $model = $this->findModel($id);
        $estudio = Estudio::find()->where([
            'id_nk' => $model->cod_estudio,
            'anyo_academico' => $anyo,
        ])->one();

        $coorDeles = $estudio->getNipCoordinadoresYDelegados();
        $usuario = Yii::$app->user->identity;
        $esCoorDele = in_array($usuario->username, $coorDeles);

        if ($model->load(Yii::$app->request->post()) && $model->update(
            true,
            [
                // Campos definidos en el modelo `Doctorado` (tabla `DATUZ_doctorado`)
                'porc_alumnos_beca_distinta',  // 1.9.b
                'alumnos_act_transv',  // 2.3.1
                'cursos_act_transv',  // 2.3.2
                'porc_alumnos_mov_out_ano',  // 3.1
                // 'porc_alumnos_mov_out_gen',  // 3.2
                'numero_expertos_int_trib',  // 4.5
                'numero_miembros_trib',  // 4.5
                'num_medio_resultados_tesis'  // 6.11
            ]
        )) {
            $nombre = Yii::$app->user->identity->username;
            Yii::info("{$nombre} ha actualizado los datos del doctorado {$model->cod_estudio}.", 'coordinadores');
            Yii::$app->session->addFlash(
                'success',
                sprintf(Yii::t('gestion', 'Se han actualizado los datos del doctorado %d.'), $model->cod_estudio)
            );

            return $this->redirect(Url::to([
                'informe/ver-doct',
                'estudio_id' => $estudio->id,
                'anyo' => $anyo,
            ]));
        } else {
            foreach ($model->getErrors() as $campo_con_errores) {
                foreach ($campo_con_errores as $error) {
                    Yii::$app->session->addFlash('error', $error);
                }
            }

            return $this->render(
                // Vista `doctorado/editar-datos.php`, que incluye la vista `doctorado/_formulario.php`
                'editar-datos',
                ['estudio' => $estudio, 'esCoorDele' => $esCoorDele, 'model' => $model]
            );
        }
    }
}
