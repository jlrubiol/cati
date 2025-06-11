<?php

namespace app\controllers;

use Yii;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;
use app\models\AcreditacionTitulaciones;
use app\models\AsignaturaCalificacion;
use app\models\AsignaturaIndicador;
use app\models\Doctorado;
use app\models\DoctoradoMacroarea;
use app\models\Encuestas;
use app\models\Estudio;
use app\models\EstudioPrevioMaster;
use app\models\Indo;
use app\models\InformePregunta;
use app\models\InformeRespuesta;
use app\models\Movilidad;
use app\models\NuevoIngreso;
use app\models\Pas;
use app\models\Plan;
use app\models\Profesorado;

/**
 * This is the class for controller "InformeRespuestaController".
 */
class InformeRespuestaController extends \app\controllers\base\InformeRespuestaController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $estudio_id = Yii::$app->request->get('estudio_id');

        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'rules' => [
                    [
                        'actions' => ['crear', 'editar', 'crear-doct', 'editar-doct'],
                        'allow' => true,
                        // Seguir bug #13598: https://github.com/yiisoft/yii2/issues/13598
                        'matchCallback' => function ($rule, $action) use ($estudio_id) {
                            return Yii::$app->user->can('editarInforme', ['estudio' => Estudio::getEstudio($estudio_id)]);
                        },
                        'roles' => ['@'],
                    ], [
                        'actions' => ['crear-iced', 'editar-iced'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) use ($estudio_id) {
                            return Yii::$app->user->can('editarInforme', ['estudio' => Estudio::getEstudio(Estudio::ICED_ESTUDIO_ID)]);
                        },
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Crea una respuesta a una pregunta del Informe de evaluación.
     * Si la creación tiene éxito, el navegador es redirigido a la página de visualización del informe.
     *
     * @return mixed
     */
    public function actionCrear($estudio_id, $informe_pregunta_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $pregunta = InformePregunta::findOne(['id' => $informe_pregunta_id]);
        $respuesta = new InformeRespuesta();

        try {
            // if ($respuesta->load(Yii::$app->request->post()) and $respuesta->save()) {
            if (Yii::$app->request->isPost) {
                $respuesta->estudio_id = $estudio_id;
                $respuesta->anyo = $estudio->anyo_academico;
                $respuesta->informe_pregunta_id = $informe_pregunta_id;
                $respuesta->apartado = $pregunta->apartado;
                $respuesta->estudio_id_nk = $estudio->id_nk;
                $respuesta->contenido = Yii::$app->request->post('contenido');
                $respuesta->save();

                $nombre_usuario = Yii::$app->user->identity->username;
                Yii::info(
                    "$nombre_usuario} ha creado una respuesta a la pregunta {$pregunta->apartado}"
                        . " del estudio {$estudio_id}.",
                    'coordinadores'
                );

                return $this->redirect([
                    $estudio->getMetodoVerInforme(),
                    'estudio_id' => $estudio_id,
                    'anyo' => $respuesta->anyo,
                    '#' => $respuesta->informe_pregunta_id,
                ]);
            } elseif (!\Yii::$app->request->isPost) {
                // $respuesta->load(Yii::$app->request->get());
                $respuesta->attributes = Yii::$app->request->get();
            }
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            // Yii::$app->getSession()->addFlash('error', $msg);
            $respuesta->addError('_exception', $msg);
        }

        $pregunta = InformePregunta::findOne(['id' => $informe_pregunta_id]);
        if (!$pregunta) {
            throw new HttpException(404, Yii::t('cati', 'No se ha encontrado esa pregunta.  ☹'));
        }

        $anyo = $estudio->anyo_academico;
        $planes = $estudio->getPlans()->where(['activo' => 1])->all();
        $lista_planes = array_column($planes, 'id_nk');

        $estructuras = Profesorado::getEstructuraProfesorado($anyo, $estudio->id_nk);

        $globales = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['between', 'ANO_ACADEMICO', $anyo - 6, $anyo])
            ->orderBy('ANO_ACADEMICO')->asArray()->all();

        $globales_abandono = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['<=', 'ANO_ACADEMICO', $anyo])
            ->andWhere('TASA_ABANDONO != 0 OR TASA_GRADUACION != 0')
            ->orderBy('ANO_ACADEMICO')
            ->asArray()->all();

        $indicadores = AsignaturaIndicador::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])->andWhere(['ANO_ACADEMICO' => $anyo])->all();
        $nuevos_ingresos = NuevoIngreso::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])->andWhere(['ANO_ACADEMICO' => $anyo])->all();

        return $this->render('crear', [
            'anyo' => $anyo,
            'centros' => $estudio->getCentros(),
            'dpsCalificaciones' => AsignaturaCalificacion::getDpsCalificaciones($anyo, $estudio),
            'dpsEstudiosPrevios' => $estudio->esMaster() ? EstudioPrevioMaster::getDpsEstudiosPrevios($anyo, $estudio->id_nk)
                                                         : null,
            'dpMovilidades' => AcreditacionTitulaciones::getDpMovilidades($anyo, $estudio->id_nk),
            'dpNuevosIngresos' => NuevoIngreso::getDpNuevosIngresos($anyo, $estudio->id_nk),
            'edades' => NuevoIngreso::getEdades($anyo, $estudio->id_nk),
            'encuestas' => Encuestas::getEncuestas($anyo, $estudio->id_nk),
            'estructuras' => $estructuras,
            'estudio' => $estudio,
            'estudio_id_nk' => $estudio->id_nk,
            'estudiosPrevios' => $estudio->esGrado() ? NuevoIngreso::getEstudiosPrevios($anyo, $estudio->id_nk)
                                                     : null,
            'evoluciones' => Profesorado::getEvolucionProfesorado($estudio->id_nk),
            'evolucionesPas' => Pas::getEvolucionesPas($anyo, $estudio->id_nk),
            'generos' => NuevoIngreso::getGeneros($anyo, $estudio->id_nk),
            'globales' => $globales,
            'globales_abandono' => $globales_abandono,
            'indicadores' => $indicadores,
            'indos' => Indo::getIndos($anyo, $estudio->id_nk),
            'lista_planes' => $lista_planes,
            'movilidades_in' => Movilidad::getMovilidadesIn($estudio->id),
            'movilidades_out' => Movilidad::getMovilidadesOut($estudio->id),
            'movilidad_porcentajes' => Movilidad::getMovilidadPorcentajes($estudio->id),
            'notasMedias' => NuevoIngreso::getNotasMedias($anyo, $estudio->id_nk),
            'nuevos_ingresos' => $nuevos_ingresos,
            'planes' => $planes,
            'pregunta' => $pregunta,
            'procedencias' => NuevoIngreso::getProcedencias($anyo, $estudio->id_nk),
            'respuesta' => $respuesta,
        ]);
    }


    /**
     * Crea una respuesta a una pregunta del Informe de evaluación de Doctorado.
     * Si la creación tiene éxito, el navegador es redirigido a la página de visualización del informe.
     *
     * @return mixed
     */
    public function actionCrearDoct($estudio_id, $informe_pregunta_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $pregunta = InformePregunta::findOne(['id' => $informe_pregunta_id]);
        $respuesta = new InformeRespuesta();

        try {
            // if ($respuesta->load(Yii::$app->request->post()) and $respuesta->save()) {
            if (Yii::$app->request->isPost) {
                $respuesta->estudio_id = $estudio_id;
                $respuesta->anyo = $estudio->anyo_academico;
                $respuesta->informe_pregunta_id = $informe_pregunta_id;
                $respuesta->apartado = $pregunta->apartado;
                $respuesta->estudio_id_nk = $estudio->id_nk;
                $respuesta->contenido = Yii::$app->request->post('contenido');
                $respuesta->save();

                $nombre_usuario = Yii::$app->user->identity->username;
                Yii::info(
                    "$nombre_usuario} ha creado una respuesta a la pregunta {$pregunta->apartado}"
                        . " del estudio {$estudio_id}.",
                    'coordinadores'
                );

                return $this->redirect([
                    $estudio->getMetodoVerInforme(),
                    'estudio_id' => $estudio_id,
                    'anyo' => $respuesta->anyo,
                    '#' => $respuesta->informe_pregunta_id,
                ]);
            } elseif (!\Yii::$app->request->isPost) {
                // $respuesta->load(Yii::$app->request->get());
                $respuesta->attributes = Yii::$app->request->get();
            }
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            // Yii::$app->getSession()->addFlash('error', $msg);
            $respuesta->addError('_exception', $msg);
        }

        $pregunta = InformePregunta::findOne(['id' => $informe_pregunta_id]);
        if (!$pregunta) {
            throw new HttpException(404, Yii::t('cati', 'No se ha encontrado esa pregunta.  ☹'));
        }

        $anyo = $estudio->anyo_academico;
        $datos = Doctorado::find()
            ->where(['cod_estudio' => $estudio->id_nk])
            ->andWhere(['between', 'ano_academico', 2012, $anyo])
            ->orderBy('ano_academico')->all();
        $ultimos_datos = Doctorado::find()->where(['cod_estudio' => $estudio->id_nk, 'ano_academico' => $anyo])->one();

        return $this->render('crear-doct', [
            'anyo' => $anyo,
            'datos' => $datos,
            'encuestas' => Encuestas::getEncuestas($anyo, $estudio->id_nk),
            'estudio' => $estudio,
            'estudio_id_nk' => $estudio->id_nk,
            'pregunta' => $pregunta,
            'respuesta' => $respuesta,
            'ultimos_datos' => $ultimos_datos,
        ]);
    }


    /**
     * Crea una respuesta a una pregunta del Informe de Calidad de los Estudios de Doctorado (ICED).
     * Si la creación tiene éxito, el navegador es redirigido a la página de visualización del informe.
     *
     * @return mixed
     */
    public function actionCrearIced($informe_pregunta_id)
    {
        $estudio = Estudio::getUltimoEstudioByNk(Estudio::ICED_ESTUDIO_ID);
        $pregunta = InformePregunta::findOne(['id' => $informe_pregunta_id]);
        $respuesta = new InformeRespuesta();

        try {
            // if ($respuesta->load(Yii::$app->request->post()) and $respuesta->save()) {
            if (Yii::$app->request->isPost) {
                $respuesta->estudio_id = $estudio->id;
                $respuesta->anyo = $pregunta->anyo;
                $respuesta->informe_pregunta_id = $informe_pregunta_id;
                $respuesta->apartado = $pregunta->apartado;
                $respuesta->estudio_id_nk = $estudio->id_nk;
                $respuesta->contenido = Yii::$app->request->post('contenido');
                $respuesta->save();

                $nombre_usuario = Yii::$app->user->identity->username;
                Yii::info(
                    "$nombre_usuario} ha creado una respuesta a la pregunta {$pregunta->apartado}"
                        . ' del ICED.',
                    'coordinadores'
                );

                return $this->redirect([
                    $estudio->getMetodoVerInforme(),
                    'anyo' => $respuesta->anyo,
                    '#' => $respuesta->informe_pregunta_id,
                ]);
            } elseif (!\Yii::$app->request->isPost) {
                // $respuesta->load(Yii::$app->request->get());
                $respuesta->attributes = Yii::$app->request->get();
            }
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            // Yii::$app->getSession()->addFlash('error', $msg);
            $respuesta->addError('_exception', $msg);
        }

        $pregunta = InformePregunta::findOne(['id' => $informe_pregunta_id]);
        if (!$pregunta) {
            throw new HttpException(404, Yii::t('cati', 'No se ha encontrado esa pregunta.  ☹'));
        }

        $anyo = $pregunta->anyo;
        $datos = DoctoradoMacroarea::find()->where(['ano_academico' => $anyo])->asArray()->all();
        $cod_conceptos = array_keys($datos[0]);
        $conceptos = array_map(function ($c) {
            return Yii::t('models', $c);
        }, $cod_conceptos);
        $datos = array_merge([$cod_conceptos], [$conceptos], $datos);
        $datos_transpuestos = array_map(null, ...$datos);

        return $this->render('crear-iced', [
            'anyo' => $anyo,
            'datos' => $datos_transpuestos,
            'estudio' => $estudio,
            'estudio_id_nk' => $estudio->id_nk,
            'pregunta' => $pregunta,
            'respuesta' => $respuesta,
        ]);
    }


    /**
     * Actualiza una respuesta de un Informe de evaluación de Grado o Máster.
     * Si la actualización tiene éxito, el navegador es redirigido a la página de visualización del informe.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionEditar($estudio_id, $informe_respuesta_id)
    {
        $respuesta = $this->findModel($informe_respuesta_id);
        if (!$respuesta) {
            throw new HttpException(404, Yii::t('cati', 'The requested page does not exist.'));
        }
        // $estudio_id se usa para comprobar que el usuario tiene permisos para acceder a este método.
        if ($respuesta->estudio_id != $estudio_id) {
            throw new ServerErrorHttpException(Yii::t(
                'cati',
                'Datos inconsistentes.  El estudio del registro no coincide con el de la petición.'
            ));
        }
        $estudio = Estudio::getEstudio($estudio_id);

        if (Yii::$app->request->isPost) {
            $respuesta->contenido = Yii::$app->request->post('contenido');
            $respuesta->update(true, ['contenido']);

            $nombre_usuario = Yii::$app->user->identity->username;
            Yii::info(
                "{$nombre_usuario} ha editado la respuesta {$respuesta->id} a la pregunta'
                    .' {$respuesta->apartado} del informe de evaluación del estudio {$estudio_id}",
                'coordinadores'
            );

            return $this->redirect([
                $estudio->getMetodoVerInforme(),
                'estudio_id' => $estudio_id,
                'anyo' => $respuesta->anyo,
                '#' => $respuesta->informe_pregunta_id,
            ]);
        }

        $anyo = $estudio->anyo_academico;
        $planes = $estudio->getPlans()->where(['activo' => 1])->all();
        $lista_planes = array_column($planes, 'id_nk');

        $estructuras = Profesorado::getEstructuraProfesorado($anyo, $estudio->id_nk);

        $globales = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['between', 'ANO_ACADEMICO', $anyo - 6, $anyo])
            ->orderBy('ANO_ACADEMICO')->asArray()->all();

        $globales_abandono = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['<=', 'ANO_ACADEMICO', $anyo])
            ->andWhere('TASA_ABANDONO != 0 OR TASA_GRADUACION != 0')
            ->orderBy('ANO_ACADEMICO')
            ->asArray()->all();

        $indicadores = AsignaturaIndicador::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])->andWhere(['ANO_ACADEMICO' => $anyo])->all();
        $nuevos_ingresos = NuevoIngreso::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])->andWhere(['ANO_ACADEMICO' => $anyo])->all();

        return $this->render('editar', [
            'anyo' => $anyo,
            'centros' => $estudio->getCentros(),
            'dpsCalificaciones' => AsignaturaCalificacion::getDpsCalificaciones($anyo, $estudio),
            'dpsEstudiosPrevios' => $estudio->esMaster() ? EstudioPrevioMaster::getDpsEstudiosPrevios($anyo, $estudio->id_nk)
                                                         : null,
            'dpMovilidades' => AcreditacionTitulaciones::getDpMovilidades($anyo, $estudio->id_nk),
            'dpNuevosIngresos' => NuevoIngreso::getDpNuevosIngresos($anyo, $estudio->id_nk),
            'edades' => NuevoIngreso::getEdades($anyo, $estudio->id_nk),
            'encuestas' => Encuestas::getEncuestas($anyo, $estudio->id_nk),
            'estructuras' => $estructuras,
            'estudio' => $estudio,
            'estudio_id_nk' => $estudio->id_nk,
            'estudiosPrevios' => $estudio->esGrado() ? NuevoIngreso::getEstudiosPrevios($anyo, $estudio->id_nk)
                                                     : null,
            'evoluciones' => Profesorado::getEvolucionProfesorado($estudio->id_nk),
            'evolucionesPas' => Pas::getEvolucionesPas($anyo, $estudio->id_nk),
            'generos' => NuevoIngreso::getGeneros($anyo, $estudio->id_nk),
            'globales' => $globales,
            'globales_abandono' => $globales_abandono,
            'indicadores' => $indicadores,
            'indos' => Indo::getIndos($anyo, $estudio->id_nk),
            'lista_planes' => $lista_planes,
            'movilidades_in' => Movilidad::getMovilidadesIn($estudio->id),
            'movilidades_out' => Movilidad::getMovilidadesOut($estudio->id),
            'movilidad_porcentajes' => Movilidad::getMovilidadPorcentajes($estudio->id),
            'notasMedias' => NuevoIngreso::getNotasMedias($anyo, $estudio->id_nk),
            'nuevos_ingresos' => $nuevos_ingresos,
            'planes' => $estudio->getPlans()->where(['activo' => 1])->all(),
            'pregunta' => $respuesta->informePregunta,
            'procedencias' => NuevoIngreso::getProcedencias($anyo, $estudio->id_nk),
            'respuesta' => $respuesta,
        ]);
    }

    /**
     * Actualiza una respuesta de un Informe de evaluación de Doctorado.
     * Si la actualización tiene éxito, el navegador es redirigido a la página de visualización del informe.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionEditarDoct($estudio_id, $informe_respuesta_id)
    {
        $respuesta = $this->findModel($informe_respuesta_id);
        if (!$respuesta) {
            throw new HttpException(404, Yii::t('cati', 'The requested page does not exist.'));
        }
        // $estudio_id se usa para comprobar que el usuario tiene permisos para acceder a este método.
        if ($respuesta->estudio_id != $estudio_id) {
            throw new ServerErrorHttpException(Yii::t(
                'cati',
                'Datos inconsistentes.  El estudio del registro no coincide con el de la petición.'
            ));
        }
        $estudio = Estudio::getEstudio($estudio_id);

        if (Yii::$app->request->isPost) {
            $respuesta->contenido = Yii::$app->request->post('contenido');
            $respuesta->update(true, ['contenido']);

            $nombre_usuario = Yii::$app->user->identity->username;
            Yii::info(
                "{$nombre_usuario} ha editado la respuesta {$respuesta->id} a la pregunta'
                    .' {$respuesta->apartado} del informe de evaluación del estudio {$estudio_id}",
                'coordinadores'
            );

            return $this->redirect([
                $estudio->getMetodoVerInforme(),
                'estudio_id' => $estudio_id,
                'anyo' => $respuesta->anyo,
                '#' => $respuesta->informe_pregunta_id,
            ]);
        }

        $anyo = $estudio->anyo_academico;
        $datos = Doctorado::find()
            ->where(['cod_estudio' => $estudio->id_nk])
            ->andWhere(['between', 'ano_academico', 2012, $anyo])
            ->orderBy('ano_academico')->all();
        $ultimos_datos = Doctorado::find()->where(['cod_estudio' => $estudio->id_nk, 'ano_academico' => $anyo])->one();

        return $this->render('editar-doct', [
            'anyo' => $anyo,
            'datos' => $datos,
            'encuestas' => Encuestas::getEncuestas($anyo, $estudio->id_nk),
            'estudio' => $estudio,
            'estudio_id_nk' => $estudio->id_nk,
            'pregunta' => $respuesta->informePregunta,
            'respuesta' => $respuesta,
            'ultimos_datos' => $ultimos_datos,
        ]);
    }

    /**
     * Actualiza una respuesta de un Informe de Calidad de los Estudios de Doctorado (ICED).
     * Si la actualización tiene éxito, el navegador es redirigido a la página de visualización del informe.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionEditarIced($informe_respuesta_id)
    {
        $respuesta = $this->findModel($informe_respuesta_id);
        $estudio = Estudio::getUltimoEstudioByNk(Estudio::ICED_ESTUDIO_ID);

        if (Yii::$app->request->isPost) {
            $respuesta->contenido = Yii::$app->request->post('contenido');
            $respuesta->update(true, ['contenido']);

            $nombre_usuario = Yii::$app->user->identity->username;
            Yii::info(
                "{$nombre_usuario} ha editado la respuesta {$respuesta->id} a la pregunta'
                    .' {$respuesta->apartado} del ICED.",
                'coordinadores'
            );

            return $this->redirect([
                $estudio->getMetodoVerInforme(),
                'estudio_id' => $estudio->id,
                'anyo' => $respuesta->anyo,
                '#' => $respuesta->informe_pregunta_id,
            ]);
        }

        $anyo = $respuesta->anyo;
        $datos = DoctoradoMacroarea::find()->where(['ano_academico' => $anyo])->asArray()->all();
        $cod_conceptos = array_keys($datos[0]);
        $conceptos = array_map(function ($c) {
            return Yii::t('models', $c);
        }, $cod_conceptos);
        $datos = array_merge([$cod_conceptos], [$conceptos], $datos);
        $datos_transpuestos = array_map(null, ...$datos);

        return $this->render('editar-iced', [
            'anyo' => $anyo,
            'datos' => $datos_transpuestos,
            'estudio' => $estudio,
            'estudio_id_nk' => $estudio->id_nk,
            'pregunta' => $respuesta->informePregunta,
            'respuesta' => $respuesta,
        ]);
    }
}
