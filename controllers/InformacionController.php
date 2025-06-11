<?php
/**
 * Controlador de la información de los estudios.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */

namespace app\controllers;

use app\models\Estudio;
use app\models\Informacion;
use app\models\Seccion;
use app\models\TipoEstudio;
use app\models\User;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * This is the class for controller "InformacionController".
 */
class InformacionController extends \app\controllers\base\InformacionController
{
    public function behaviors()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $estudio_id = $request->post('estudio_id');
        } else {
            // GET, HEAD, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH.
            $estudio_id = $request->get('estudio_id');
        }

        return \yii\helpers\ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'rules' => [
                        [
                            'actions' => ['ver'],
                            'allow' => true,
                        ], [
                            'actions' => ['editar-infos', 'editar', 'guardar'],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) use ($estudio_id) {
                                return Yii::$app->user->can('editarInforme', ['estudio' => Estudio::getEstudio($estudio_id)]);
                            },
                            'roles' => ['@'],
                        ], [
                            'actions' => ['editar-infos-en-masa', 'editar-en-masa', 'guardar-en-masa'],
                            'allow' => true,
                            'roles' => ['gradoMaster', 'escuelaDoctorado', 'unidadCalidad'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Muestra la información de un estudio.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionVer($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $informaciones = Informacion::find()->where(['estudio_id_nk' => $estudio->id_nk])->orderBy('seccion_id')->all();
        $enSecciones = function ($infos, $min, $max) {
            return array_filter(
                $infos,
                function ($info) use ($min, $max) {
                    return $info->seccion_id >= $min && $info->seccion_id < $max;
                }
            );
        };

        $pag1 = $enSecciones($informaciones, 10, 20);
        $pag2 = $enSecciones($informaciones, 20, 30);
        $pag3 = $enSecciones($informaciones, 30, 40);
        $pag4 = $enSecciones($informaciones, 40, 50);
        $pag5 = $enSecciones($informaciones, 50, 60);
        $pag6 = $enSecciones($informaciones, 60, 70);
        $pag7 = $enSecciones($informaciones, 70, 80);

        return $this->render(
            'ver', [
                'estudio' => $estudio,
                'pag1' => $pag1,
                'pag2' => $pag2,
                'pag3' => $pag3,
                'pag4' => $pag4,
                'pag5' => $pag5,
                'pag6' => $pag6,
                'pag7' => $pag7,
            ]
        );
    }

    /**
     * Muestra enlaces para editar cada una de las infos de un estudio 
     */
    public function actionEditarInfos($estudio_id, $tipo)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $secciones = Seccion::find()->where(['tipo' => $tipo])->orderBy('pagina, orden')->all();
        $paginas = [];
        foreach ($secciones as $seccion) {
            if (// Ciencia y Tecnología de los Alimentos tiene secciones extra
                (!in_array($seccion->id, [43, 44, 45, 46, 47, 48, 49, 65]) or 112 == $estudio->id_nk)
                and (!in_array($seccion->id, Informacion::SECCIONES_RESTRINGIDAS) or Yii::$app->user->can('gestor'))
            ) {
                $paginas[$seccion->pagina][$seccion->id] = $seccion;
            }
        }

        return $this->render(
            'editar-infos',
            [
                'estudio' => $estudio,
                'paginas' => $paginas,
                'tipo' => $tipo,
            ]
        );
    }

    /**
     * Muestra el formulario para editar una sección de la información de un estudio 
     */
    public function actionEditar($estudio_id, $seccion_id, $tipo)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        if (in_array($seccion_id, Informacion::SECCIONES_RESTRINGIDAS) and !Yii::$app->user->can('gestor')) {
            throw new ForbiddenHttpException(Yii::t('cati', 'No tiene permisos para editar esta información.  😱'));
        }

        $info = Informacion::find()->where(['estudio_id_nk' => $estudio->id_nk, 'seccion_id' => $seccion_id])->one();
        if (!$info) {
            $info = new Informacion(
                [
                    // 'estudio_id' => $estudio->id,
                    'estudio_id_nk' => $estudio->id_nk,
                    'seccion_id' => $seccion_id,
                    'texto' => '',
                ]
            );
        }

        return $this->render(
            'editar',
            [
                'estudio' => $estudio,
                'info' => $info,
                'tipo' => $tipo,
            ]
        );
    }

    public function actionGuardar()
    {
        $language = Yii::$app->language;
        $request = Yii::$app->request;
        $estudio = Estudio::getEstudio($request->post('estudio_id'));

        $info = Informacion::find()->where(
            [
                'estudio_id_nk' => $estudio->id_nk,
                'seccion_id' => $request->post('seccion_id'),
            ]
        )->one();

        if (!$info) {
            $info = new Informacion(
                [
                    // 'estudio_id' => $estudio->id,
                    'estudio_id_nk' => $estudio->id_nk,
                    'seccion_id' => $request->post('seccion_id'),
                ]
            );
        }

        $info->language = $language;
        $info->texto = $request->post('texto');
        if ($info->save()) {
            $nombre = Yii::$app->user->identity->username;
            Yii::info(sprintf("$nombre ha actualizado la información del estudio %d", $estudio->id_nk), 'gestion');
        }

        if (Estudio::DOCT_TIPO_ESTUDIO_ID == $estudio->tipoEstudio_id) {
            $funcion_ver = 'estudio/ver-doct';
        } else {
            $funcion_ver = 'estudio/ver';
        }

        return $this->redirect(
            [
                $funcion_ver,
                'id' => $estudio->id_nk,
            ]
        );
    }

    /**
     * Muestra enlaces para editar cada una de las infos de todos los estudios de un tipo
     */
    public function actionEditarInfosEnMasa($tipoEstudio_id)
    {
        $tipoEstudio = TipoEstudio::getTipoEstudio($tipoEstudio_id);

        if (in_array($tipoEstudio_id, [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID])) {
            $tipo = 'grado-master';
        } else {
            $tipo = 'doctorado';
        }
        $secciones = Seccion::find()->where(['tipo' => $tipo])->orderBy('pagina, orden')->all();
        $paginas = [];
        foreach ($secciones as $seccion) {
            // Ciencia y Tecnología de los Alimentos tiene secciones extra
            if (!(in_array($seccion->id, [43, 44, 45, 46, 47, 48, 49, 65]))) {
                $paginas[$seccion->pagina][$seccion->id] = $seccion;
            }
        }

        return $this->render(
            'editar-infos-en-masa',
            [
                'paginas' => $paginas,
                'tipoEstudio' => $tipoEstudio,
            ]
        );
    }

    /**
     * Muestra un formulario para editar una sección de todos los estudios de un tipo.
     */
    public function actionEditarEnMasa($tipoEstudio_id, $seccion_id)
    {
        $tipoEstudio = TipoEstudio::getTipoEstudio($tipoEstudio_id);

        $seccion = Seccion::findOne(['id' => $seccion_id]);
        if (!$seccion) {
            throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado esa sección.  ☹'));
        }

        return $this->render(
            'editar-en-masa',
            [
                'tipoEstudio' => $tipoEstudio,
                'seccion' => $seccion,
            ]
        );
    }

    /**
     * Reemplaza el texto de una sección en todos los estudios de un tipo.
     */
    public function actionGuardarEnMasa()
    {
        $language = Yii::$app->language;
        $request = Yii::$app->request;
        $tipoEstudio_id = $request->post('tipoEstudio_id');
        $seccion_id = $request->post('seccion_id');
        $texto = $request->post('texto');

        $idnkEstudios = Estudio::getIdnkEstudiosDelTipo($tipoEstudio_id);

        $exito = true;
        foreach ($idnkEstudios as $id_nk) {
            $info = Informacion::find()->where(
                [
                    'estudio_id_nk' => $id_nk,
                    'seccion_id' => $seccion_id,
                ]
            )->one();

            if (!$info) {
                $info = new Informacion(
                    [
                        'id' => null,
                        'estudio_id_nk' => $id_nk,
                        'seccion_id' => $seccion_id,
                    ]
                );
            }

            $info->language = $language;
            $info->texto = $texto;
            if (!$info->save()) {
                $exito = false;
                Yii::info(
                    'gestion',
                    "No fue posible guardar la sección {$info->seccion_id}"
                    . " de información del estudio {$info->estudio_id_nk}"
                );
            }
        }

        if ($exito) {
            $nombre = Yii::$app->user->identity->username;
            Yii::info("$nombre ha guardado informacion en masa de los estudios de tipo $tipoEstudio_id", 'gestion');
            Yii::$app->session->addFlash(
                'success',
                Yii::t('gestion', 'Se ha guardado con éxito el nuevo texto de la sección.')
            );
        } else {
            Yii::$app->session->addFlash(
                'error',
                Yii::t('gestion', 'Error al guardar el nuevo texto de la sección.  😨')
            );
        }

        return $this->redirect(
            [
                'informacion/editar-infos-en-masa',
                'tipoEstudio_id' => $tipoEstudio_id,
            ]
        );
    }
}
