<?php
/**
 * /home/quique/Devel/cati/cati/runtime/giiant/49eb2de82346bc30092f584268252ed2
 *
 * @package default
 */

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use app\models\DoctoradoMacroarea;
use app\models\Estudio;

/**
 * This is the class for controller "DoctoradoMacroareaController".
 */
class DoctoradoMacroareaController extends \app\controllers\base\DoctoradoMacroareaController
{
    public function behaviors()
    {
        $request = Yii::$app->request;

        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['editar-datos'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            $estudio = Estudio::find()->where([
                                'id_nk' => Estudio::ICED_ESTUDIO_ID
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

    /** Actualiza los datos de doctorado de una rama y año. */
    public function actionEditarDatos($anyo, $rama_id)
    {
        $model = DoctoradoMacroarea::find()->where(['ano_academico' => $anyo, 'cod_rama_conocimiento' => $rama_id])->one();

        // Si se modifican los atributos manuales, hay que editar también
        // views/doctorado-macroarea/_formulario.php
        if ($model->load(Yii::$app->request->post()) && $model->update(
            true,
            [
                # Campos definidos en el modelo `DoctoradoMacroarea` (tabla `DATUZ_doctorado_macroarea`)
                'porc_alumnos_beca_distinta', # 1.9.b
                'alumnos_act_transv',  # 2.3.1
                'cursos_act_transv',  # 2.3.2
                'porc_alumnos_mov_out_ano',  # 3.1
                // 'porc_alumnos_mov_out_gen',  # 3.2
                'numero_expertos_int_trib',  # 4.5
                'numero_miembros_trib',  # 4.5
                'num_medio_resultados_tesis',  # 6.11
            ]
        )) {
            $nombre = Yii::$app->user->identity->username;
            Yii::info("{$nombre} ha actualizado los datos de doctorado de la macroarea {$rama_id}.", 'coordinadores');
            Yii::$app->session->addFlash(
                'success',
                sprintf(Yii::t('gestion', 'Se han actualizado los datos de la macroárea.'))
            );

            return $this->redirect(Url::to([
                'informe/ver-iced',
                'anyo' => $anyo,
            ]));
        }

        foreach ($model->getErrors() as $campo_con_errores) {
            foreach ($campo_con_errores as $error) {
                Yii::$app->session->addFlash('error', $error);
            }
        }

        return $this->render(
            // Vista `doctorado-macroarea/editar-datos.php`, que incluye la vista `doctorado-macroarea/_formulario.php`
            'editar-datos',
            ['anyo' => $anyo, 'model' => $model]
        );
    }
}
