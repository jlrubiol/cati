<?php

namespace app\controllers;

use app\controllers\base\CatiController;
use app\controllers\PlanMejoraController;
use app\models\AcreditacionTitulaciones;
use app\models\AsignaturaCalificacion;
use app\models\AsignaturaIndicador;
use app\models\Calendario;
use app\models\Cifice;
use app\models\Doctorado;
use app\models\DoctoradoMacroarea;
use app\models\Encuestas;
use app\models\Estudio;
use app\models\EstudioPrevioMaster;
use app\models\Indo;
use app\models\InformePregunta;
use app\models\InformePublicado;
use app\models\InformeRespuesta;
use app\models\Movilidad;
use app\models\NuevoIngreso;
use app\models\Pas;
use app\models\Plan;
use app\models\PlanPregunta;
use app\models\PlanRespuesta;
use app\models\Profesorado;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use Yii;
// use mikehaertl\pdftk\Pdf as Pdftk;
// use mikehaertl\wkhtmlto\Pdf;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * This is the class for controller "InformeController".
 */
class InformeController extends CatiController
{
    /**
     * {@inheritdoc}
     */
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
                            'actions' => [
                                'cerrar', 'previsualizar', 'cerrar-doct', 'cerrar-iced',
                                'editar', 'editar-doct', 'editar-iced', 'guardar',
                            ],
                            'allow' => true,
                            // Seguir bug #13598: https://github.com/yiisoft/yii2/issues/13598
                            'matchCallback' => function ($rule, $action) use ($estudio_id) {
                                return Yii::$app->user->can('editarInforme', ['estudio' => Estudio::getEstudio($estudio_id)]);
                            },
                            'roles' => ['@'],
                        ], [
                            'actions' => ['cargar-a-zaguan'],
                            'allow' => true,
                            'roles' => ['escuelaDoctorado', 'unidadCalidad'],
                        ], [
                            'actions' => [
                                'calificaciones', 'doct-nuevo-ingreso', 'doct-matriculados', 'doct-resultados-formacion',
                                'estructura-profesorado', 'evolucion-profesorado', 'indicadores',
                                'estudio-previo', 'nota-media', 'planes-movilidad',
                                'plazas-nuevo-ingreso', 'resultados-academicos', 'globales', 'globales-abandono',
                                'globales-adaptacion', 'globales-creditos', 'globales-duracion',
                                'globales-exito', 'globales-nuevo-ingreso', 'procedencia', 'genero', 'edad',
                                'innovacion-docente', 'movilidad', 'pas', 'encuestas',
                                'ver', 'ver-doct', 'ver-iced',
                                'marc-xml', 'marc-xml-doct', 'marc-xml-iced'
                            ],
                            'allow' => true,
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Distribución de calificaciones
     */
    public function actionCalificaciones($estudio_id, $anyo)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $dpsCalificaciones = AsignaturaCalificacion::getDpsCalificaciones(intval($anyo), $estudio);

        return $this->render(
            'calificaciones',
            [
                'anyo' => intval($anyo),
                'dpsCalificaciones' => $dpsCalificaciones,
                'estudio' => $estudio,
            ]
        );
    }

    /**
     * Muestra la tabla de Estudiantes de nuevo ingreso de Doctorado
     */
    public function actionDoctNuevoIngreso($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $datos = Doctorado::find()
            ->where(['cod_estudio' => $estudio->id_nk])
            ->andWhere(['between', 'ano_academico', 2012, $estudio->anyo_academico])
            ->orderBy('ano_academico')->all();
        $ultimos_datos = Doctorado::find()
            ->where(['cod_estudio' => $estudio->id_nk, 'ano_academico' => $estudio->anyo_academico])
            ->one();
        return $this->render(
            'doct/ver_tabla',
            [
                'anyo' => $estudio->anyo_academico,
                'caption' => Yii::t('cati', 'Estudiantes de nuevo ingreso'),
                'datos' => $datos,
                'estudio' => $estudio,
                'tabla' => '_nuevo_ingreso',
                'ultimos_datos' => $ultimos_datos,
            ]
        );
    }

    /**
     * Muestra la tabla de Estudiantes matriculados de Doctorado
     */
    public function actionDoctMatriculados($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $datos = Doctorado::find()
            ->where(['cod_estudio' => $estudio->id_nk])
            ->andWhere(['between', 'ano_academico', 2012, $estudio->anyo_academico])
            ->orderBy('ano_academico')->all();
        $ultimos_datos = Doctorado::find()
            ->where(['cod_estudio' => $estudio->id_nk, 'ano_academico' => $estudio->anyo_academico])
            ->one();
        return $this->render(
            'doct/ver_tabla',
            [
                'anyo' => $estudio->anyo_academico,
                'caption' => Yii::t('cati', 'Estudiantes matriculados'),
                'datos' => $datos,
                'estudio' => $estudio,
                'tabla' => '_matriculados',
                'ultimos_datos' => $ultimos_datos,
            ]
        );
    }

    /**
     * Muestra la tabla de Resultados de la formación de Doctorado.
     * Tabla `DATUZ_doctorado`, actualizada vía `gestion/actualizar-datos-doctorado`
     * que ejecuta el script `datuz/doct2cati.php`.
     * Origen: transformación `doctorado/tit_acreditacion_doctorado_plan`.
     */
    public function actionDoctResultadosFormacion($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $datos = Doctorado::find()
            ->where(['cod_estudio' => $estudio->id_nk])
            ->andWhere(['between', 'ano_academico', 2012, $estudio->anyo_academico])
            ->orderBy('ano_academico')->all();
        $ultimos_datos = Doctorado::find()
            ->where(['cod_estudio' => $estudio->id_nk, 'ano_academico' => $estudio->anyo_academico])
            ->one();
        return $this->render(
            'doct/ver_tabla',
            [
                'anyo' => $estudio->anyo_academico,
                'caption' => Yii::t('cati', 'Resultados de la formación'),
                'datos' => $datos,
                'estudio' => $estudio,
                'tabla' => '_formacion',
                'ultimos_datos' => $ultimos_datos,
            ]
        );
    }

    /** Muestra el perfil de ingreso de los estudiantes de un estudio: edad */
    public function actionEdad($anyo, $estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        return $this->render(
            'edad',
            [
                'estudio' => $estudio,
                'edades' => NuevoIngreso::getEdades($anyo, $estudio->id_nk),
            ]
        );
    }

    /**
     * Muestra los datos de satisfacción de los estudiantes de un estudio, por centro.
     * Tabla `DATUZ_encuestas`, rellenada por la pasarela `json_datuz_titulaciones_encuesta.ktr`.
     * Origen: transformación `titulaciones/tit_encuesta_titulacion`.
     */
    public function actionEncuestas($anyo, $estudio_id_nk)
    {
        $estudio = Estudio::findOne(['id_nk' => $estudio_id_nk, 'anyo_academico' => $anyo]);

        return $this->render(
            'encuestas',
            [
                'estudio' => $estudio,
                'encuestas' => Encuestas::getEncuestas($anyo, $estudio->id_nk),
            ]
        );
    }


    /**
     * Muestra las tablas de estructura del profesorado de un estudio y año.
     */
    public function actionEstructuraProfesorado($anyo, $estudio_id_nk)
    {
        $nombre_estudio = Estudio::getNombreByNk($estudio_id_nk);
        $estructuras = Profesorado::getEstructuraProfesorado($anyo, $estudio_id_nk);

        if (empty(array_filter($estructuras))) {
            throw new NotFoundHttpException(
                Yii::t(
                    'cati',
                    'No se han encontrado las estructuras del profesorado para ese estudio y año.  ☹'
                )
            );
        }

        return $this->render(
            'estructura-profesorado',
            [
                'anyo' => intval($anyo),
                'estructuras' => $estructuras,
                'estudio_id_nk' => $estudio_id_nk,
                'nombre_estudio' => $nombre_estudio,
            ]
        );
    }

    /**
     * Muestra la evolución del profesorado de un estudio
     */
    public function actionEvolucionProfesorado($estudio_id_nk)
    {
        $nombre_estudio = Estudio::getNombreByNk($estudio_id_nk);
        $evoluciones = Profesorado::getEvolucionProfesorado($estudio_id_nk);

        return $this->render(
            'evolucion-profesorado',
            [
                'estudio_id_nk' => $estudio_id_nk,
                'evoluciones' => $evoluciones,
                'nombre_estudio' => $nombre_estudio,
            ]
        );
    }


    /** Muestra el perfil de ingreso de los estudiantes de un estudio: género */
    public function actionGenero($anyo, $estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        return $this->render(
            'genero',
            [
                'estudio' => $estudio,
                'generos' => NuevoIngreso::getGeneros($anyo, $estudio->id_nk),
            ]
        );
    }


    /**
     * Análisis de los indicadores del título
     */
    public function actionIndicadores($estudio_id, $anyo)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        $indicadores = AsignaturaIndicador::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])->andWhere(['ANO_ACADEMICO' => $anyo])->all();

        if (!$indicadores) {
            throw new NotFoundHttpException(
                Yii::t(
                    'cati',
                    'No se han encontrado indicadores para ese estudio y año.  ☹'
                )
            );
        }

        $centros = $estudio->getCentros();

        return $this->render(
            'indicadores',
            [
                'centros' => $centros,
                'estudio' => $estudio,
                'indicadores' => $indicadores,
            ]
        );
    }

    /**
     * Muestra la tabla de participación del profesorado en Proyectos de Innovación Docente.
     * Tabla `DATUZ_INDO`, rellenada por la pasarela ``
     * Origen: transformación ``
     */
    public function actionInnovacionDocente($anyo, $estudio_id_nk)
    {
        $estudio = Estudio::findOne(['id_nk' => $estudio_id_nk, 'anyo_academico' => $anyo]);

        return $this->render(
            'innovacion-docente',
            [
                'estudio' => $estudio,
                'indos' => Indo::getIndos($anyo, $estudio->id_nk),
            ]
        );
    }

    /**
     * Muestra los estudios previos de los alumnos de un grado o máster.
     */
    public function actionEstudioPrevio($anyo, $estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        if ($estudio->esGrado()) {
            $estudiosPrevios = NuevoIngreso::getEstudiosPrevios($anyo, $estudio->id_nk);
            return $this->render(
                'estudio_previo',
                [
                    'anyo' => intval($anyo),
                    'estudio' => $estudio,
                    'estudiosPrevios' => $estudiosPrevios,
                ]
            );
        }

        // Máster
        $dpsEstudiosPrevios = EstudioPrevioMaster::getDpsEstudiosPrevios($anyo, $estudio->id_nk);
        return $this->render(
            'estudio_previo',
            [
                'anyo' => intval($anyo),
                'dpsEstudiosPrevios' => $dpsEstudiosPrevios,
                'estudio' => $estudio,
            ]
        );
    }

    /**
     * Muestra los estudiantes de un estudio en planes de movilidad.
     * Tabla `DATUZ_movilidad`, rellenada por la pasarela [...]
     * Origen: transformación `tit_movilidad`
     */
    public function actionMovilidad($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        return $this->render(
            'movilidades',
            [
                'estudio' => $estudio,
                'movilidades_in' => Movilidad::getMovilidadesIn($estudio->id),
                'movilidades_out' => Movilidad::getMovilidadesOut($estudio->id),
                'movilidad_porcentajes' => Movilidad::getMovilidadPorcentajes($estudio->id),
            ]
        );
    }

    public function actionNotaMedia($anyo, $estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        return $this->render(
            'nota_media',
            [
                'estudio' => $estudio,
                'notasMedias' => NuevoIngreso::getNotasMedias($anyo, $estudio->id_nk),
            ]
        );
    }

    /**
     * Muestra la evolución del PAS de un estudio
     *
     * Tabla `DATUZ_pas`, rellenada por la pasarela [...]
     * Origen: transformación `tit_pas`
     */
    public function actionPas($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        return $this->render(
            'pas',
            [
                'estudio' => $estudio,
                'evolucionesPas' => Pas::getEvolucionesPas($estudio->anyo_academico, $estudio->id_nk),
            ]
        );
    }

    public function actionPlanesMovilidad($anyo, $estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        return $this->render(
            'planes_movilidad',
            [
                'estudio' => $estudio,
                'dpMovilidades' => AcreditacionTitulaciones::getDpMovilidades($anyo, $estudio->id_nk),
            ]
        );
    }

    public function actionPlazasNuevoIngreso($estudio_id, $anyo)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        $nuevos_ingresos = NuevoIngreso::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])->andWhere(['ANO_ACADEMICO' => $anyo])->all();

        if (!$nuevos_ingresos) {
            throw new NotFoundHttpException(
                Yii::t(
                    'cati',
                    'No se han encontrado las plazas de nuevo ingreso ofertadas para ese estudio y año.  ☹'
                )
            );
        }

        return $this->render(
            'plazas_nuevo_ingreso',
            [
                'estudio' => $estudio,
                'nuevos_ingresos' => $nuevos_ingresos,
            ]
        );
    }

    /** Muestra el perfil de ingreso de los estudiantes de un estudio: procedencia (residencia familiar) */
    public function actionProcedencia($anyo, $estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        return $this->render(
            'procedencia',
            [
                'estudio' => $estudio,
                'procedencias' => NuevoIngreso::getProcedencias($anyo, $estudio->id_nk),
            ]
        );
    }

    /* Resultados académicos de años anteriores */
    public function actionResultadosAcademicos($estudio_id)
    {
        $anyo_academico = Calendario::getAnyoAcademico();
        $estudio = Estudio::getEstudio($estudio_id);
        $anyos = AsignaturaIndicador::anyosAnteriores($estudio->id_nk, $anyo_academico);

        return $this->render(
            'resultados_academicos',
            [
                'estudio' => $estudio,
                'anyos' => $anyos,
            ]
        );
    }

    public function actionGlobales($estudio_id)
    {
        // Al final de enero se publican los resultados del curso actual que ya se conocen
        // (oferta/nuevo ingreso/matrícula, créditos reconocidos...)
        // Algunos resultados (éxito/rendimiento/eficiencia, abandono/graduación...)
        // no se conocerán de forma definitiva hastá después de los exámenes de septiembre.
        $anyo_resultados = date('m') < 10 ? date('Y') - 2 : date('Y') - 1;

        $estudio = Estudio::getEstudio($estudio_id);

        $globales = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->orderBy('ANO_ACADEMICO')
            ->asArray()->all();

        if (!$globales) {
            throw new NotFoundHttpException(
                Yii::t(
                    'cati',
                    'No se han encontrado datos globales para ese estudio.  ☹'
                )
            );
        }

        $globales_abandono = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['<=', 'ANO_ACADEMICO', $anyo_resultados])
            ->andWhere('TASA_ABANDONO != 0 OR TASA_GRADUACION != 0')
            ->orderBy('ANO_ACADEMICO')
            ->asArray()->all();

        $globales_definitivos = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['<=', 'ANO_ACADEMICO', $anyo_resultados])
            ->orderBy('ANO_ACADEMICO')
            ->asArray()->all();

        $centros = $estudio->getCentros();

        return $this->render(
            'globales',
            [
                'estudio' => $estudio,
                'globales' => $globales,
                'globales_abandono' => $globales_abandono,
                'globales_definitivos' => $globales_definitivos,
                'centros' => $centros,
            ]
        );
    }

    public function actionGlobalesAbandono($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        // Al final de enero se publican los resultados del curso actual que ya se conocen
        // (oferta/nuevo ingreso/matrícula, créditos reconocidos...)
        // Algunos resultados (éxito/rendimiento/eficiencia, abandono/graduación...)
        // no se conocerán hastá después de los exámenes de septiembre.
        $anyo_resultados = date('m') < 10 ? date('Y') - 2 : date('Y') - 1;
        $globales_abandono = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['<=', 'ANO_ACADEMICO', $anyo_resultados])
            ->andWhere('TASA_ABANDONO != 0 OR TASA_GRADUACION != 0')
            ->orderBy('ANO_ACADEMICO')
            ->asArray()->all();

        if (!$globales_abandono) {
            throw new NotFoundHttpException(
                Yii::t(
                    'cati',
                    'No se han encontrado datos globales para ese estudio.  ☹'
                )
            );
        }

        $centros = $estudio->getCentros();

        return $this->render(
            'globales_abandono',
            [
                'estudio' => $estudio,
                'globales_abandono' => $globales_abandono,
                'centros' => $centros,
            ]
        );
    }

    public function actionGlobalesAdaptacion($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        // Al final de enero se publican los resultados del curso actual que ya se conocen
        // (oferta/nuevo ingreso/matrícula, créditos reconocidos...)
        // Algunos resultados (éxito/rendimiento/eficiencia, abandono/graduación...)
        // no se conocerán hastá después de los exámenes de septiembre.
        $anyo_resultados = date('m') < 10 ? date('Y') - 2 : date('Y') - 1;
        $globales = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['<=', 'ANO_ACADEMICO', $anyo_resultados])
            ->orderBy('ANO_ACADEMICO')
            ->asArray()->all();

        if (!$globales) {
            throw new NotFoundHttpException(
                Yii::t(
                    'cati',
                    'No se han encontrado datos globales para ese estudio.  ☹'
                )
            );
        }

        $centros = $estudio->getCentros();

        return $this->render(
            'globales_adaptacion',
            [
                'estudio' => $estudio,
                'globales' => $globales,
                'centros' => $centros,
            ]
        );
    }

    public function actionGlobalesCreditos($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        $globales = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['between', 'ANO_ACADEMICO', $estudio->anyo_academico - 5, $estudio->anyo_academico])
            ->orderBy('ANO_ACADEMICO')->asArray()->all();

        if (!$globales) {
            throw new NotFoundHttpException(
                Yii::t(
                    'cati',
                    'No se han encontrado datos globales para ese estudio.  ☹'
                )
            );
        }

        $centros = $estudio->getCentros();

        return $this->render(
            'globales_creditos',
            [
                'estudio' => $estudio,
                'globales' => $globales,
                'centros' => $centros,
            ]
        );
    }

    public function actionGlobalesDuracion($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        // Al final de enero se publican los resultados del curso actual que ya se conocen
        // (oferta/nuevo ingreso/matrícula, créditos reconocidos...)
        // Algunos resultados (éxito/rendimiento/eficiencia, abandono/graduación...)
        // no se conocerán hastá después de los exámenes de septiembre.
        $anyo_resultados = date('m') < 10 ? date('Y') - 2 : date('Y') - 1;
        $globales = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['<=', 'ANO_ACADEMICO', $anyo_resultados])
            ->orderBy('ANO_ACADEMICO')
            ->asArray()->all();

        if (!$globales) {
            throw new NotFoundHttpException(
                Yii::t(
                    'cati',
                    'No se han encontrado datos globales para ese estudio.  ☹'
                )
            );
        }

        $centros = $estudio->getCentros();

        return $this->render(
            'globales_duracion',
            [
                'estudio' => $estudio,
                'globales' => $globales,
                'centros' => $centros,
            ]
        );
    }

    public function actionGlobalesExito($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        // Al final de enero se publican los resultados del curso actual que ya se conocen
        // (oferta/nuevo ingreso/matrícula, créditos reconocidos...)
        // Algunos resultados (éxito/rendimiento/eficiencia, abandono/graduación...)
        // no se conocerán hastá después de los exámenes de septiembre.
        $anyo_resultados = date('m') < 10 ? date('Y') - 2 : date('Y') - 1;
        $globales = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['<=', 'ANO_ACADEMICO', $anyo_resultados])
            ->orderBy('ANO_ACADEMICO')
            ->asArray()->all();

        if (!$globales) {
            throw new NotFoundHttpException(
                Yii::t(
                    'cati',
                    'No se han encontrado datos globales para ese estudio.  ☹'
                )
            );
        }

        $centros = $estudio->getCentros();

        return $this->render(
            'globales_exito',
            [
                'estudio' => $estudio,
                'globales' => $globales,
                'centros' => $centros,
            ]
        );
    }

    public function actionGlobalesNuevoIngreso($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        $globales = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['between', 'ANO_ACADEMICO', $estudio->anyo_academico - 5, $estudio->anyo_academico])
            ->orderBy('ANO_ACADEMICO')->asArray()->all();

        if (!$globales) {
            throw new NotFoundHttpException(
                Yii::t(
                    'cati',
                    'No se han encontrado datos globales para ese estudio.  ☹'
                )
            );
        }

        $centros = $estudio->getCentros();

        return $this->render(
            'globales_nuevo_ingreso',
            [
                'estudio' => $estudio,
                'globales' => $globales,
                'centros' => $centros,
            ]
        );
    }

    /**
     * Muestra el informe de evaluación de un estudio (de Grado o Máster) y año.
     */
    public function actionVer($estudio_id, $anyo)
    {
        Url::remember();
        $estudio = Estudio::getEstudio($estudio_id);
        $language = Yii::$app->language;
        $informePublicado = InformePublicado::find()
            ->where(['estudio_id' => $estudio_id, 'anyo' => $anyo, 'language' => $language])->one();

        $nueva_version = isset($informePublicado) ? $informePublicado->version + 1 : 1;
        $nombre_nueva_version = InformePublicado::getNombreVersion($estudio->getTipoEstudio(), $nueva_version);
        $estudio_anterior = Estudio::findOne(['id_nk' => $estudio->id_nk, 'anyo_academico' => $anyo - 1]);

        // El campo apartado es una cadena, por lo que se ordena alfabéticamente
        // y 10 va después de 1 en lugar de después de 9.
        // Con esta expresión convertimos a tipo numérico los primeros 3 caracteres
        // y ordenamos correctamente.  En MS SQLServer usar SUBSTRING().
        $exp = new Expression('CAST(SUBSTR(apartado, 1, 3) AS DECIMAL), apartado');

        $preguntas = InformePregunta::find()
            ->where(['anyo' => $anyo, 'tipo' => $estudio->getTipoEstudio()])
            ->orderBy($exp)
            ->all();
        if (!$preguntas) {
            throw new NotFoundHttpException(
                Yii::t(
                    'cati',
                    'No se han encontrado las preguntas del informe de evaluación para ese año.  ☹'
                )
            );
        }

        $respuestas = InformeRespuesta::find()
            ->where(['estudio_id' => $estudio_id])->andWhere(['anyo' => $anyo])
            ->orderBy($exp)
            ->all();

        $respuestas2 = [];
        foreach ($respuestas as $respuesta) {
            $respuestas2[$respuesta->informe_pregunta_id] = $respuesta;
        }

        $version_maxima = InformePublicado::MAX_VERSION_INFORME;
        $mostrar_botones = (
            ($nueva_version <= $version_maxima) and Yii::$app->user->can('editarInforme', ['estudio' => $estudio])
        );

        $nuevos_ingresos = NuevoIngreso::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])->andWhere(['ANO_ACADEMICO' => $anyo])->all();

        # Resultados de aprendizaje / Distribución de calificaciones
        # Tabla `DATUZ_asignatura_calificacion`, rellenada por `scripts/datuz/datuz2cati.php`
        # Origen: transformación `tit_asignatura_calificacion`
        $dpsCalificaciones = AsignaturaCalificacion::getDpsCalificaciones(intval($anyo), $estudio);

        $planes = $estudio->getPlans()->where(['activo' => 1])->all();
        $lista_planes = array_column($planes, 'id_nk');

        # Personal académico / Tabla de estructura del profesorado
        # Tabla `DATUZ_profesorado`, rellenada por `scripts/ods/JSON_estructura_profesorado.ktr`
        # Origen: transformación `tit_estructura_profesorado`
        $estructuras = Profesorado::getEstructuraProfesorado($anyo, $estudio->id_nk);

        $globales = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['between', 'ANO_ACADEMICO', $anyo - 5, $anyo])
            ->orderBy('ANO_ACADEMICO', 'COD_CENTRO')
            ->asArray()->all();

        $globales_abandono = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['between', 'ANO_ACADEMICO', $anyo - 5, $anyo])
            ->andWhere('TASA_ABANDONO != 0 OR TASA_GRADUACION != 0')
            ->orderBy('ANO_ACADEMICO', 'COD_CENTRO')
            ->asArray()->all();

        # Análisis de los indicadores del título
        # Tabla `DATUZ_asignatura_indicador`
        $indicadores = AsignaturaIndicador::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['ANO_ACADEMICO' => $anyo])
            ->orderBy('COD_CENTRO')->all();

        /* ACCIONES DEL PAIM DEL AÑO ANTERIOR */
        $preguntas_paim = PlanPregunta::find()
            ->where([
                'anyo' => $anyo - 1,
                'tipo' => $estudio->getTipoEstudio()
            ])
            ->orderBy($exp)
            ->all();
        if (!$preguntas_paim) {
            throw new HttpException(
                404,
                Yii::t('cati', 'No se han encontrado las preguntas del plan de mejora para este año.  ☹')
            );
        }
        # Quitamos el apartado de fecha de aprobación CGC
        array_pop($preguntas_paim);

        /*
        $respuestas_paim_1 = PlanRespuesta::find()
            ->where(['estudio_id' => $estudio->id, 'anyo' => $anyo - 1])
            ->andWhere(['is', 'tipo_modificacion_id', null])
            ->all();
        // TODO: Quitar respuesta de fecha
        $respuestas_paim_2 = PlanRespuesta::find()
            ->where(['estudio_id' => $estudio->id, 'anyo' => $anyo - 1])
            ->andWhere(['is not', 'tipo_modificacion_id', null])
            ->all();
        */
        $respuestas_paim = PlanRespuesta::find()
            ->where(['estudio_id_nk' => $estudio->id_nk, 'anyo' => $anyo - 1])
            ->orderBy($exp)
            ->all();

        $respuestas_paim_2 = [];
        foreach ($respuestas_paim as $respuesta_paim) {
            $respuestas_paim_2[$respuesta_paim->plan_pregunta_id][] = $respuesta_paim;
        }

        return $this->render(
            'ver',
            [
                'anyo' => intval($anyo),
                'estudio' => $estudio,
                'centros' => $estudio->getCentros(),
                'cifices' => Cifice::getCifices($anyo, $estudio->id_nk),
                'dpsCalificaciones' => $dpsCalificaciones,
                'dpsEstudiosPrevios' => $estudio->esMaster() ? EstudioPrevioMaster::getDpsEstudiosPrevios($anyo, $estudio->id_nk)
                                                             : null,
                'dpMovilidades' => AcreditacionTitulaciones::getDpMovilidades($anyo, $estudio->id_nk),
                'dpNuevosIngresos' => NuevoIngreso::getDpNuevosIngresos($anyo, $estudio->id_nk),
                'edades' => NuevoIngreso::getEdades($anyo, $estudio->id_nk),
                'encuestas' => Encuestas::getEncuestas($anyo, $estudio->id_nk),
                'estructuras' => $estructuras,
                'estudio_anterior' => $estudio_anterior,
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
                'mostrar_botones' => $mostrar_botones,
                'movilidades_in' => Movilidad::getMovilidadesIn($estudio->id),
                'movilidades_out' => Movilidad::getMovilidadesOut($estudio->id),
                'movilidad_porcentajes' => Movilidad::getMovilidadPorcentajes($estudio->id),
                'nombre_nueva_version' => $nombre_nueva_version,
                'notasMedias' => NuevoIngreso::getNotasMedias($anyo, $estudio->id_nk),
                'nueva_version' => $nueva_version,
                'nuevos_ingresos' => $nuevos_ingresos,
                'planes' => $planes,
                'preguntas' => $preguntas,
                'procedencias' => NuevoIngreso::getProcedencias($anyo, $estudio->id_nk),
                'respuestas' => $respuestas2,
                'preguntas_paim' => $preguntas_paim,
                'respuestas_paim' => $respuestas_paim_2,
            ]
        );
    }

    /**
     * Muestra el informe de un Programa de Doctorado.
     */
    public function actionVerDoct($estudio_id, $anyo)
    {
        Url::remember();
        $estudio = Estudio::getEstudio($estudio_id);
        $language = Yii::$app->language;
        $informePublicado = InformePublicado::find()
            ->where(['estudio_id' => $estudio_id, 'anyo' => $anyo, 'language' => $language])->one();

        $nueva_version = isset($informePublicado) ? $informePublicado->version + 1 : 1;
        $nombre_nueva_version = InformePublicado::getNombreVersion($estudio->getTipoEstudio(), $nueva_version);
        $estudio_anterior = Estudio::findOne(['id_nk' => $estudio->id_nk, 'anyo_academico' => $anyo - 1]);

        // El campo apartado es una cadena, por lo que se ordena alfabéticamente
        // y 10 va después de 1 en lugar de después de 9.
        // Con esta expresión convertimos a tipo numérico los primeros 3 caracteres
        // y ordenamos correctamente.  En MS SQLServer usar SUBSTRING().
        $exp = new Expression('CAST(SUBSTR(apartado, 1, 3) AS DECIMAL), apartado');

        $preguntas = InformePregunta::find()
            ->where(['anyo' => $anyo, 'tipo' => $estudio->getTipoEstudio()])
            ->orderBy($exp)
            ->all();
        if (!$preguntas) {
            throw new NotFoundHttpException(
                Yii::t(
                    'cati',
                    'No se han encontrado las preguntas del informe de evaluación para ese año.  ☹'
                )
            );
        }

        $respuestas = InformeRespuesta::find()
            ->where(['estudio_id' => $estudio_id, 'anyo' => $anyo])
            ->orderBy($exp)->all();

        $respuestas2 = [];
        foreach ($respuestas as $respuesta) {
            $respuestas2[$respuesta->informe_pregunta_id] = $respuesta;
        }

        $version_maxima = InformePublicado::MAX_VERSION_INFORME_DOCT;
        $mostrar_botones = (
            ($nueva_version <= $version_maxima) and Yii::$app->user->can('editarInforme', ['estudio' => $estudio])
        );

        $datos = Doctorado::find()
            ->where(['cod_estudio' => $estudio->id_nk])
            ->andWhere(['between', 'ano_academico', $anyo-5, $anyo])
            ->orderBy('ano_academico')->all(); // ->asArray()->all();
        $ultimos_datos = Doctorado::find()->where(['cod_estudio' => $estudio->id_nk, 'ano_academico' => $anyo])->one();

        /* ACCIONES DEL PAIM DEL AÑO ANTERIOR */
        $preguntas_paim = PlanPregunta::find()
            ->where([
                'anyo' => $anyo - 1,
                'tipo' => $estudio->getTipoEstudio()
            ])
            ->orderBy($exp)
            ->all();
        if (!$preguntas_paim) {
            throw new HttpException(
                404,
                Yii::t('cati', 'No se han encontrado las preguntas del plan de mejora para este año.  ☹')
            );
        }
        # Quitamos el apartado de fecha de aprobación CGC
        array_pop($preguntas_paim);

        $respuestas_paim = PlanRespuesta::find()
            ->where(['estudio_id_nk' => $estudio->id_nk, 'anyo' => $anyo - 1])
            ->orderBy($exp)
            ->all();

        $respuestas_paim_2 = [];
        foreach ($respuestas_paim as $respuesta_paim) {
            $respuestas_paim_2[$respuesta_paim->plan_pregunta_id][] = $respuesta_paim;
        }

        return $this->render(
            'doct/ver-doct',
            [
                'anyo' => intval($anyo),
                'datos' => $datos,
                'encuestas' => Encuestas::getEncuestas($anyo, $estudio->id_nk),
                'estudio' => $estudio,
                'estudio_anterior' => $estudio_anterior,
                'mostrar_botones' => $mostrar_botones,
                'nombre_nueva_version' => $nombre_nueva_version,
                'nueva_version' => $nueva_version,
                'preguntas' => $preguntas,
                'respuestas' => $respuestas2,
                'ultimos_datos' => $ultimos_datos,
                'preguntas_paim' => $preguntas_paim,
                'respuestas_paim' => $respuestas_paim_2,
            ]
        );
    }

    private function transponerMatriz($matriz, $clase)
    {
        $model = new $clase;
        $cod_conceptos = array_keys($matriz[0]);
        $conceptos = array_map(
            function ($c) use ($model) {
                // return Yii::t('models', $model->getAttributeLabel($c));
                return Yii::t('models', $c);
            },
            $cod_conceptos
        );
        $matriz = array_merge([$cod_conceptos], [$conceptos], $matriz);
        $matriz_transpuesta = array_map(null, ...$matriz);
        // En PHP < 5.6 el array bidimensional se puede transponer así:
        //   array_unshift($matriz, null);
        //   $matriz_transpuesta = call_user_func_array('array_map', $matriz);
        return $matriz_transpuesta;
    }

    /**
     * Muestra el Informe de la Calidad de los Estudios de Doctorado (ICED).
     */
    public function actionVerIced($anyo)
    {
        Url::remember();
        $estudio = Estudio::getUltimoEstudioByNk(Estudio::ICED_ESTUDIO_ID);
        $language = Yii::$app->language;
        $informePublicado = InformePublicado::find()
            ->where(['estudio_id' => Estudio::ICED_ESTUDIO_ID, 'anyo' => $anyo, 'language' => $language])->one();

        $nueva_version = isset($informePublicado) ? $informePublicado->version + 1 : 1;

        // El campo apartado es una cadena, por lo que se ordena alfabéticamente
        // y 10 va después de 1 en lugar de después de 9.
        // Con esta expresión convertimos a tipo numérico los primeros 3 caracteres
        // y ordenamos correctamente.  En MS SQLServer usar SUBSTRING().
        $exp = new Expression('CAST(SUBSTR(apartado, 1, 3) AS DECIMAL), apartado');

        $preguntas = InformePregunta::find()
            ->where(['anyo' => $anyo, 'tipo' => $estudio->getTipoEstudio()])
            ->orderBy($exp)
            ->all();
        if (!$preguntas) {
            throw new NotFoundHttpException(
                Yii::t(
                    'cati',
                    'No se han encontrado las preguntas del ICED para ese año.  ☹'
                )
            );
        }

        $respuestas = InformeRespuesta::find()
            ->where(['estudio_id' => $estudio->id, 'anyo' => $anyo])
            ->orderBy($exp)->all();

        $respuestas2 = [];
        foreach ($respuestas as $respuesta) {
            $respuestas2[$respuesta->informe_pregunta_id] = $respuesta;
        }

        $version_maxima = InformePublicado::MAX_VERSION_INFORME_ICED;
        // Las personas con permisos para editar el ICED son las que son delegados del plan 9999 (tabla `agente`).
        $mostrar_botones = (
            ($nueva_version <= $version_maxima) and Yii::$app->user->can('editarInforme', ['estudio' => $estudio])
        );

        $datos = DoctoradoMacroarea::find()->where(['ano_academico' => $anyo])->asArray()->all();
        $cod_conceptos = array_keys($datos[0]);
        $conceptos = array_map(
            function ($c) {
                return Yii::t('models', $c);
            },
            $cod_conceptos
        );
        $datos = array_merge([$cod_conceptos], [$conceptos], $datos);
        $datos_transpuestos = array_map(null, ...$datos);
        /*
        // En PHP < 5.6 el array bidimensional se puede transponer así:
        array_unshift($datos, null);
        $datos_transpuestos = call_user_func_array('array_map', $datos);
        */

        return $this->render(
            'iced/ver-iced',
            [
                'anyo' => intval($anyo),
                'datos' => $datos_transpuestos,
                'estudio' => $estudio,
                'mostrar_botones' => $mostrar_botones,
                'nueva_version' => $nueva_version,
                'preguntas' => $preguntas,
                'respuestas' => $respuestas2,
            ]
        );
    }

    public function actionCerrar($estudio_id, $anyo)
    {
        $language = Yii::$app->language;
        $estudio = Estudio::getEstudio($estudio_id);

        $informePublicado = InformePublicado::find()
            ->where(['estudio_id' => $estudio_id, 'anyo' => $anyo, 'language' => $language])->one();
        if (!$informePublicado) {
            $informePublicado = new InformePublicado(
                [
                    'estudio_id' => $estudio_id,
                    'anyo' => intval($anyo),
                    'language' => $language,
                    'version' => 0,
                    'estudio_id_nk' => $estudio->id_nk,
                ]
            );
        }
        $version_maxima = $informePublicado->getVersionMaxima();
        if ($informePublicado->version >= $version_maxima) {
            throw new ServerErrorHttpException('Este informe ya está en su versión final. 😨');
        }
        $nuevaVersion = $informePublicado->version + 1;
        $nombre_nueva_version = InformePublicado::getNombreVersion($estudio->getTipoEstudio(), $nuevaVersion);
        $vista = $estudio->getMetodoVerInforme();

        $estudio_anterior = Estudio::find()->where(['anyo_academico' => $anyo - 1, 'id_nk' => $estudio->id_nk])->one();
        if ($estudio_anterior) {
            # Es obligatorio introducir el `estado` en las acciones del paim_anterior, antes de cerrar el IEC.
            # (salvo en el apartado de fecha de aprobación por la CGC, que no tiene estado).
            # En las acciones que no modifican título, el campo `valor_alcanzado` también es obligatorio.
            $plan_respuestas = PlanRespuesta::find()
                ->where(['estudio_id' => $estudio_anterior->id, 'anyo' => $anyo - 1])
                ->all();
            foreach ($plan_respuestas as $respuesta) {
                $campos_pregunta = array_map(function ($a) { return trim($a); }, explode(',', $respuesta->planPregunta->atributos));
                if (is_null($respuesta->estado) and !($respuesta->planPregunta->atributos == 'fecha')) {
                    $mensaje = "Debe introducir el estado en cada una de las acciones del PAIM del año anterior (apartado 0 del IEC).\n";
                    Yii::$app->session->addFlash('error', Yii::t('app', $mensaje));
                    return $this->redirect([$vista, 'estudio_id' => $estudio_id, 'anyo' => $anyo]);
                }
                if (in_array('valores_a_alcanzar', $campos_pregunta) and is_null($respuesta->valores_alcanzados)) {
                    $mensaje = "Debe introducir los valores alcazados en las acciones del PAIM del año anterior (apartado 0 del IEC).\n";
                    Yii::$app->session->addFlash('error', Yii::t('app', $mensaje));
                    return $this->redirect([$vista, 'estudio_id' => $estudio_id, 'anyo' => $anyo]);
                }
            }
        }

        # Para cerrar la versión definitiva, es obligatorio haber introducido los datos de aprobación
        if ($nuevaVersion === $version_maxima) {
            $preguntas_obligatorias = InformePregunta::find()
                ->where(['anyo' => $anyo, 'tipo' => $estudio->getTipoEstudio(), 'oblig_def' => true])
                ->all();
            foreach ($preguntas_obligatorias as $pregunta) {
                $respuesta = InformeRespuesta::find()
                    ->where(['estudio_id' => $estudio_id, 'anyo' => $anyo])
                    ->andWhere(['informe_pregunta_id' => $pregunta->id])
                    ->all();
                if (!$respuesta) {
                    $mensaje = "Es obligatorio responder el apartado {$pregunta->apartado}.\n";
                    Yii::$app->session->addFlash('error', Yii::t('app', $mensaje));
                    return $this->redirect([$vista, 'estudio_id' => $estudio_id, 'anyo' => $anyo]);
                }
            }
        }

        $preinforme = $this->generarPdfPreinforme($estudio_id, $anyo);
        $rutaPreinforme = $preinforme['ruta'];
        $dirInformes = Yii::getAlias('@webroot') . '/pdf/informes';
        $dirInformesAnyo = "$dirInformes/$anyo";
        $nombreInforme = "informe-{$language}-{$estudio->id_nk}-v{$nuevaVersion}.pdf";
        $rutaInforme = "{$dirInformesAnyo}/{$nombreInforme}";
        $urlInforme = Url::base(true) . "/pdf/informes/{$anyo}/{$nombreInforme}";

        // Si el estudio es de tipo doctorado, pasar a la función `cerrarDoct()`.
        if ($estudio->esDoctorado()) {
            return $this->cerrarDoct(
                $anyo,
                $estudio,
                $nombre_nueva_version,
                $nuevaVersion,
                $rutaPreinforme,
                $informePublicado,
                $rutaInforme,
                $urlInforme
            );
        } elseif ($estudio->esIced()) {
            $nombreInforme = "iced-{$language}-v{$nuevaVersion}.pdf";
            $rutaInforme = "{$dirInformesAnyo}/{$nombreInforme}";
            $urlInforme = Url::base(true) . "/pdf/informes/{$anyo}/{$nombreInforme}";

            return $this->cerrarIced(
                $anyo,
                $estudio,
                $nombre_nueva_version,
                $nuevaVersion,
                $rutaPreinforme,
                $informePublicado,
                $rutaInforme,
                $urlInforme
            );
        }

        /*
        // El informe se obtiene concatenando el preinforme y las encuestas.
        $dirEncuestas = Yii::getAlias('@webroot') . '/pdf/encuestas/' . $anyo;
        $pdfs = ["$rutaPreinforme"];
        $planes = Plan::find()->where(['estudio_id' => $estudio_id, 'activo' => 1])->all();
        foreach ($planes as $plan) {
            $ensenanza_file = sprintf(
                '%s/ensenanza/%d/%d_InformeEnsenanzaTitulacion.pdf',
                $dirEncuestas,
                $plan->centro->id,
                $plan->id_nk
            );
            $movilidad_file = $dirEncuestas . '/movilidad/' . $plan->centro->id . '/' . $plan->id_nk
              . '_InformeMovilidad.pdf';
            $practicas_file = sprintf(
                '%s/practicas/%d/%d_InformePracticasTitulacion.pdf',
                $dirEncuestas,
                $plan->centro->id,
                $plan->id_nk
            );
            $pas_file = sprintf(
                '%s/satisfaccionPAS/%d/%d_InformeSatisfaccionPAS.pdf',
                $dirEncuestas,
                $plan->centro->id,
                $plan->centro->id  // La satisfacción del PAS es por centro, no por plan
            );
            $pdi_file = sprintf(
                '%s/satisfaccionPDI/%d/%d_InformeSatisfaccionPDI.pdf',
                $dirEncuestas,
                $plan->centro->id,
                $plan->id_nk
            );
            $est_file = $dirEncuestas . '/satisfaccionTitulacion/' . $plan->centro->id . '/' . $plan->id_nk
                . '_InformeSatisfaccionTitulacionEstudiantes.pdf';

            foreach ([$ensenanza_file, $movilidad_file, $practicas_file, $pas_file, $pdi_file, $est_file] as $f) {
                if (file_exists($f)) {
                    $pdfs[] = "$f";
                }
            }
        }

        $output = [];
        $error = false;
        exec('pdftk ' . implode(' ', $pdfs) . " cat output $rutaInforme 2>&1", $output, $error);
        if ($error) {
            throw new ServerErrorHttpException(implode("\n", $output));
        }
        */

        // Ya no es necesario anexar las encuestas, así que simplemente movemos el preinforme a informe.
        # rename($rutaPreinforme, $rutaInforme);

        // Incorporamos el anexo A-Q212_2 (Descripción de los indicadores)
        $rutaDescripciones = Yii::getAlias('@webroot') . '/pdf/procedimientos/A-Q212-2.pdf';
        exec("qpdf $rutaPreinforme --replace-input --pages $rutaPreinforme $rutaDescripciones --");

        // Incorporamos los PAIMs a los que se refiere el IEC (1, 3 o 6 años)
        $primer_anyo = $estudio->anyo_academico - $estudio->anyos_evaluacion;
        for ($a = $primer_anyo; $a < $estudio->anyo_academico; $a++) {
            $estudio_anterior = Estudio::find()->where(['anyo_academico' => $a, 'id_nk' => $estudio->id_nk])->one();

            if ($estudio_anterior) {
                # Generamos un nuevo PDF del PAIM del año anterior, usando la vista `plan-mejora/ver-completado.php`
                # y lo adjuntamos al PDF del preinforme.
                $plan = PlanMejoraController::generarPdfPlan($estudio_anterior->id, $a, True);
                $rutaPlan = $plan['ruta'];
                # exec("qpdf --empty --pages $rutaPreinforme $rutaPlan -- $rutaInforme");  # Sin bookmarks
                exec("qpdf $rutaPreinforme --replace-input --pages $rutaPreinforme $rutaPlan --"); # Conserva bookmarks del fichero de entrada
            }
        }

        # Movemos el preinforme a informe, con la descripción de los indicadores y los anexos de los PAIM de años anteriores.
        rename($rutaPreinforme, $rutaInforme);

        // Guardar número de versión publicada
        $informePublicado->version = $nuevaVersion;
        $informePublicado->save();

        $nombre_usuario = Yii::$app->user->identity->username;
        Yii::info(
            "{$nombre_usuario} ha cerrado la versión {$nuevaVersion} del informe del estudio {$estudio_id}",
            'coordinadores'
        );

        /*
         * Enviar mensajes de correo electrónico
         *
         * La versión 1 se envía a:
         *  - Coordinadores de los planes del estudio
         *  - Presidente de Garantía de Calidad del estudio,
         *  - Decano/Director del centro correspondiente,
         *
         * La versión 2 se envía además a:
         *  - los expertos del rector
         */
        $coordinadores = $estudio->getCoordinadoresYDelegados();
        $presidentes = $estudio->getPresidentesGarantiaYDelegados();
        $decanos = $estudio->getDecanos();
        $destinatarios = array_merge($coordinadores, $presidentes, $decanos);
        # $destinatarios[] = Yii::$app->params['mailvr'];  # Vicerrector de Política Académica

        if (2 == $nuevaVersion) {
            $expertos = $estudio->getExpertosRector();
            $destinatarios = array_merge($destinatarios, $expertos);
        }

        $destinatarios = array_unique($destinatarios);
        $this->enviarCorreo($destinatarios, $estudio, $anyo, $nombre_nueva_version, $nuevaVersion, $urlInforme, $rutaInforme);

        // Redirigir al PDF
        // return $this->redirect($urlInforme);

        // Redirigir a la página «Mis estudios» del usuario, con un mensaje flash de éxito.
        $mensaje = "Se ha presentado con éxito la versión {$nombre_nueva_version} del IEC.\n";
        $mensaje .= "Puede ver el PDF en {$urlInforme} .";
        Yii::$app->session->addFlash(
            'success',
            Yii::t('gestion', $mensaje)
        );
        return $this->redirect(Url::to(['//gestion/mis-estudios']));
    }

    public function actionPrevisualizar($estudio_id, $anyo)
    {
        $preinforme = $this->generarPdfPreinforme($estudio_id, $anyo);
        // Redirigir al PDF
        return $this->redirect($preinforme['url']);
    }

    private function generarPdfPreinforme($estudio_id, $anyo)
    {
        $language = Yii::$app->language;
        $estudio = Estudio::getEstudio($estudio_id);

        $dirInformes = Yii::getAlias('@webroot') . '/pdf/informes';
        $dirInformesAnyo = "$dirInformes/$anyo";
        if (!is_dir($dirInformesAnyo)) {
            mkdir($dirInformesAnyo);
            copy("{$dirInformes}/index.html", "{$dirInformesAnyo}/index.html");
        }
        $nombrePreinforme = "preinforme-{$language}-{$estudio->id_nk}.pdf";
        $rutaPreinforme = "{$dirInformesAnyo}/{$nombrePreinforme}";
        $urlPreinforme = Url::base(true) . "/pdf/informes/{$anyo}/{$nombrePreinforme}";
        $vista = $estudio->getMetodoVerInforme();
        $url_informe = Url::to(
            [
                $vista,
                'estudio_id' => $estudio_id,
                'anyo' => $anyo,
            ],
            true
        );  // true: Absolute URL

        # TODO Considerar <https://github.com/pontedilana/php-weasyprint>
        # $command = "/usr/bin/weasyprint --debug \"{$url_informe}\" {$rutaPreinforme}";
        # exec($command, $output, $retval);
        $process = new Process(['/usr/bin/weasyprint', $url_informe, $rutaPreinforme]);
        $process->setTimeout(60);  // timeout in seconds
        $process->run();
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        /*
        $rutaCookies = tempnam($dirInformesAnyo, $language);

        // Generar una cookie-jar para establecer el idioma
        $pdf = new Pdf(['cookie-jar' => $rutaCookies]);
        $pdf->addPage(Url::to(['language/set2', 'language' => $language], true));  // true: URL absoluta.
        $pdf->setOptions(
            [
                'binary' => Yii::$app->params['wkhtmltopdf'],
                // 'inputArg'
                'print-media-type',
            ]
        );
        if (!$pdf->saveAs('/dev/null')) {
            throw new ServerErrorHttpException($pdf->getError());
        }

        // Crear el manejador del PDF
        $pdf = new Pdf(
            [
                'cookie-jar' => $rutaCookies,
                'binary' => Yii::$app->params['wkhtmltopdf'],
                'print-media-type',
            ]
        );

        // Crear el PDF del preinforme
        $vista = $estudio->getMetodoVerInforme();
        $pdf->addPage(
            Url::to(
                [
                    $vista,
                    'estudio_id' => $estudio_id,
                    'anyo' => $anyo,
                ],
                true
            )
        );  // true: Absolute URL
        if (!$pdf->saveAs($rutaPreinforme)) {
            throw new ServerErrorHttpException($pdf->getError());
        }
        unlink($rutaCookies);
        */

        return ['ruta' => $rutaPreinforme, 'url' => $urlPreinforme];
    }

    private function enviarCorreo($destinatarios, $estudio, $anyo, $nombre_nueva_version, $nuevaVersion, $urlInforme, $rutaInforme)
    {
        if (empty($destinatarios)) {
            return;
        }

        if ($estudio->esDoctorado()) {
            $asunto = "Informe de Evaluación de la Calidad de $estudio->nombre";
            $plantilla = 'informe-doct-cerrado';  // @app/mail/informe-doct-cerrado.php
        } elseif ($estudio->esIced()) {
            $asunto = 'Informe de la calidad de los Estudios de Doctorado y de sus diferentes programas';
            $plantilla = 'iced-cerrado';  // @app/mail/iced-cerrado.php
        } else {
            $asunto = "Informe de evaluación (v. $nombre_nueva_version) de $estudio->nombre";
            $plantilla = 'informe-cerrado';  // @app/mail/informe-cerrado.php
        }

        $mensaje = Yii::$app->mailer->compose(
            $plantilla,
            [
                'estudio' => $estudio,
                'anyo' => intval($anyo),
                'nombre_nueva_version' => $nombre_nueva_version,
                'version' => $nuevaVersion,
                'url_pdf' => $urlInforme,
            ]
        )->setFrom([Yii::$app->params['adminEmail'] => 'Robot Estudios'])
            ->setTo($destinatarios)
            ->setSubject($asunto);
        // ->setTextBody($texto)
        // ->setHtmlBody('<b>HTML content</b>');

        $mensaje->attach($rutaInforme);
        $mensaje->send();
    }

    public function cerrarDoct(
        $anyo,
        $estudio,
        $nombre_nueva_version,
        $nuevaVersion,
        $rutaPreinforme,
        $informePublicado,
        $rutaInforme,
        $urlInforme
    ) {
        // Concatenar las encuestas al informe
        // $dirEncuestas = Yii::getAlias('@webroot').'/pdf/encuestas/'.$anyo;
        # $pdfs = ["$rutaPreinforme"];
        /*
        $planes = Plan::find()->where(['estudio_id' => $estudio_id, 'activo' => 1])->all();
        foreach ($planes as $plan) {
            $foo_file = $dirEncuestas.'/bar/'.$plan->id_nk.'_baz.pdf';

            foreach ([$foo_file] as $f) {
                if (file_exists($f)) {
                    $pdfs[] = "$f";
                }
            }
        }
        */
        # $ficheros = implode(' ', $pdfs);
        # exec("qpdf --empty --pages $ficheros -- $rutaInforme");

        // Ya no es necesario anexar las encuestas, así que simplemente movemos el preinforme a informe.
        # rename($rutaPreinforme, $rutaInforme);

        // Incorporamos el anexo A-Q212_2 (Descripción de los indicadores)
        $rutaDescripciones = Yii::getAlias('@webroot') . '/pdf/procedimientos/A-Q212-2.pdf';
        exec("qpdf $rutaPreinforme --replace-input --pages $rutaPreinforme $rutaDescripciones --");

        // Incorporamos los PAIMs a los que se refiere el IEC (1, 3 o 6 años)
        $primer_anyo = $estudio->anyo_academico - $estudio->anyos_evaluacion;
        for ($a = $primer_anyo; $a < $estudio->anyo_academico; $a++) {
            $estudio_anterior = Estudio::find()->where(['anyo_academico' => $a, 'id_nk' => $estudio->id_nk])->one();

            if ($estudio_anterior) {
                # Generamos un nuevo PDF del PAIM del año anterior, usando la vista `plan-mejora/ver-completado.php`
                # y lo adjuntamos al PDF del preinforme.
                $plan = PlanMejoraController::generarPdfPlan($estudio_anterior->id, $a, True);
                $rutaPlan = $plan['ruta'];
                # exec("qpdf --empty --pages $rutaPreinforme $rutaPlan -- $rutaInforme");  # Sin bookmarks
                exec("qpdf $rutaPreinforme --replace-input --pages $rutaPreinforme $rutaPlan --"); # Conserva bookmarks del fichero de entrada
            }
        }

        # Movemos el preinforme a informe, con la descripción de los indicadores y los anexos de los PAIM de años anteriores.
        rename($rutaPreinforme, $rutaInforme);

        // Guardar número de versión publicada
        $informePublicado->version = $nuevaVersion;
        $informePublicado->save();

        $nombre_usuario = Yii::$app->user->identity->username;
        Yii::info(
            "{$nombre_usuario} ha cerrado la versión {$nuevaVersion} del informe del estudio {$estudio->id}",
            'coordinadores'
        );

        /*
         * Enviar mensajes de correo electrónico
         *
         * El informe se envía a:
         *  - Coordinador del programa de doctorado
         *  - Director de la Escuela de Doctorado
         */
        $destinatarios = [
            $estudio->plans[0]->email_coordinador,
            Yii::$app->params['diredoc'],
            # Yii::$app->params['presiDoct'],  # Presidente de la Comisión de Doctorado
        ];

        $this->enviarCorreo($destinatarios, $estudio, $anyo, $nombre_nueva_version, $nuevaVersion, $urlInforme, $rutaInforme);

        // Redirigir al PDF
        return $this->redirect($urlInforme);
    }

    public function cerrarIced(
        $anyo,
        $estudio,
        $nombre_nueva_version,
        $nuevaVersion,
        $rutaPreinforme,
        $informePublicado,
        $rutaInforme,
        $urlInforme
    ) {
        // Incorporamos el anexo A-Q212_2 (Descripción de los indicadores)
        $rutaDescripciones = Yii::getAlias('@webroot') . '/pdf/procedimientos/A-Q212-2.pdf';
        exec("qpdf $rutaPreinforme --replace-input --pages $rutaPreinforme $rutaDescripciones --");

        copy($rutaPreinforme, $rutaInforme);

        // Guardar número de versión publicada
        $informePublicado->version = $nuevaVersion;
        $informePublicado->save();

        /*
         * Enviar mensajes de correo electrónico
         *
         * La versión 1 se envía a:
         *  - Director de la Escuela de Doctorado
         *  - Vicerrector de política académica
         *
         * La versión 2 se envía además a:
         *  - PAS de la Escuela de Doctorado
         *  - Coordinadores de los programas de doctorado
         *  - Área de Calidad y Mejora <uzcalidad@unizar.es>
         */
        $destinatarios = [
            Yii::$app->params['diredoc'],
            Yii::$app->params['presiDoct'],
        ];

        if (2 == $nuevaVersion) {
            $destinatarios[] = Yii::$app->params['pasedoc'];
            $coordinadores = $estudio->getTodosCoordinadoresDoctorado();
            $destinatarios = array_merge($destinatarios, $coordinadores);
            $destinatarios[] = 'uzcalidad@unizar.es';
        }

        $destinatarios = array_unique($destinatarios);
        $this->enviarCorreo($destinatarios, $estudio, $anyo, $nombre_nueva_version, $nuevaVersion, $urlInforme, $rutaInforme);

        // Redirigir al PDF
        return $this->redirect($urlInforme);
    }

    public function actionEditar($estudio_id, $anyo)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        $informePublicado = InformePublicado::find()
            ->where(['estudio_id' => $estudio_id])->andWhere(['anyo' => $anyo])->one();
        if (isset($informePublicado) and $informePublicado->version >= $informePublicado->getVersionMaxima()) {
            throw new ServerErrorHttpException('Este informe ya está en su versión final. 😨');
        }

        // El campo apartado es una cadena, por lo que se ordena alfabéticamente
        // y 10 va después de 1 en lugar de después de 9.
        // Con esta expresión convertimos a tipo numérico los primeros 3 caracteres
        // y ordenamos correctamente.  En MS SQLServer usar SUBSTRING().
        $exp = new Expression('CAST(SUBSTR(apartado, 1, 3) AS DECIMAL), apartado');
        $preguntas = InformePregunta::find()
            ->where(['anyo' => $anyo, 'tipo' => 'grado-master'])
            ->orderBy($exp)->all();
        if (!$preguntas) {
            throw new NotFoundHttpException(
                Yii::t('cati', 'No se han encontrado las preguntas del informe para este año.')
            );
        }

        $respuestas = InformeRespuesta::find()
            ->where(['estudio_id' => $estudio_id])->andWhere(['anyo' => $anyo])
            ->orderBy($exp)->all();

        // Grado
        $nuevos_ingresos = NuevoIngreso::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])->andWhere(['ANO_ACADEMICO' => $anyo])->all();

        $planes = Plan::find()->where(['estudio_id' => $estudio_id, 'activo' => 1])->all();
        $planes_list = array_column($planes, 'id_nk');

        $estructuras = Profesorado::getEstructuraProfesorado($anyo, $estudio->id_nk);

        $globales = AcreditacionTitulaciones::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])
            ->andWhere(['between', 'ANO_ACADEMICO', $anyo - 6, $anyo])
            ->orderBy('ANO_ACADEMICO')->asArray()->all();

        $indicadores = AsignaturaIndicador::find()
            ->where(['COD_ESTUDIO' => $estudio->id_nk])->andWhere(['ANO_ACADEMICO' => $anyo])->all();

        return $this->render(
            'editar',
            [
                'anyo' => intval($anyo),
                'centros' => $estudio->getCentros(),
                'dpsCalificaciones' => AsignaturaCalificacion::getDpsCalificaciones(intval($anyo), $estudio),
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
                'indicadores' => $indicadores,
                'indos' => Indo::getIndos($anyo, $estudio->id_nk),
                'lista_planes' => $planes_list,
                'movilidades_in' => Movilidad::getMovilidadesIn($estudio->id),
                'movilidades_out' => Movilidad::getMovilidadesOut($estudio->id),
                'movilidad_porcentajes' => Movilidad::getMovilidadPorcentajes($estudio->id),
                'notasMedias' => NuevoIngreso::getNotasMedias($anyo, $estudio->id_nk),
                'nuevos_ingresos' => $nuevos_ingresos,
                'planes' => $planes,
                'preguntas' => $preguntas,
                'procedencias' => NuevoIngreso::getProcedencias($anyo, $estudio->id_nk),
                'respuestas' => $respuestas,
            ]
        );
    }

    /**
     * Muestra el formulario de edición de los informes de doctorado.
     */
    public function actionEditarDoct($estudio_id, $anyo)
    {
        $estudio = Estudio::getEstudio($estudio_id);

        $informePublicado = InformePublicado::find()
            ->where(['estudio_id' => $estudio_id, 'anyo' => $anyo])->one();
        if (isset($informePublicado) and $informePublicado->version >= $informePublicado->getVersionMaxima()) {
            throw new ServerErrorHttpException('Este informe ya está en su versión final. 😨');
        }

        $exp = new Expression('CAST(SUBSTR(apartado, 1, 3) AS DECIMAL), apartado');
        $preguntas = InformePregunta::find()
            ->where(['anyo' => $anyo, 'tipo' => 'doctorado'])
            ->orderBy($exp)->all();
        $respuestas = InformeRespuesta::find()
            ->where(['estudio_id' => $estudio_id, 'anyo' => $anyo])
            ->orderBy($exp)->all();

        $datos = Doctorado::find()->where(['cod_estudio' => $estudio->id_nk, 'ano_academico' => $anyo])->one();
        if (!$datos) {
            $datos = [];
        }

        return $this->render(
            'editar-doct',
            [
                'anyo' => intval($anyo),
                'datos' => $datos,
                'encuestas' => Encuestas::getEncuestas($anyo, $estudio->id_nk),
                'estudio' => $estudio,
                'preguntas' => $preguntas,
                'respuestas' => $respuestas,
            ]
        );
    }


    public function actionGuardar()
    {
        $request = Yii::$app->request;
        $cookies = $request->cookies; // get the cookie collection
        $language = $cookies->getValue('language', 'es');

        $estudio_id = intval($request->post('estudio_id'));
        $estudio = Estudio::getEstudio($estudio_id);
        $tipo = $estudio->getTipoEstudio();
        $vista = $estudio->getMetodoVerInforme();

        $exp = new Expression('CAST(SUBSTR(apartado, 1, 3) AS DECIMAL), apartado');
        $preguntas = InformePregunta::find()
            ->where(['anyo' => $request->post('anyo'), 'tipo' => $tipo])
            ->orderBy($exp)->all();

        foreach ($preguntas as $pregunta) {
            $respuesta = InformeRespuesta::find()->where(
                [
                    'estudio_id' => $estudio_id,
                    'anyo' => $request->post('anyo'),
                    'informe_pregunta_id' => $pregunta->id,
                ]
            )->one();

            if (!$respuesta) {
                $respuesta = new InformeRespuesta();

                $respuesta->estudio_id = $estudio_id;
                $respuesta->anyo = $request->post('anyo');
                $respuesta->informe_pregunta_id = $pregunta->id;
                $respuesta->apartado = $pregunta->apartado;
            }

            $respuesta->language = $language;
            $respuesta->contenido = $request->post($pregunta->id);

            $respuesta->save();
        }

        $usuario = Yii::$app->user->identity;
        $nombre = $usuario->username;
        Yii::info("{$nombre} ha guardado un informe del estudio {$estudio_id}.", 'coordinadores');

        return $this->redirect(
            Url::to(
                [
                    $vista,
                    'estudio_id' => $estudio_id,
                    'anyo' => $request->post('anyo'),
                ]
            )
        );
    }

    /**
     * Genera un MarcXML para exportar los informes de un año.
     *
     * Puede generar el MarcXML de un grado/máster dado o de todos.
     */
    public function actionMarcXml($anyo, $estudio_id = null)
    {
        Yii::$app->language = 'es';
        $dir_informes = Yii::getAlias('@webroot') . "/pdf/informes/{$anyo}";
        if ($estudio_id) {
            $estudios = [Estudio::getEstudio($estudio_id)];
        } else {
            $estudios = Estudio::find()
                ->where(['anyo_academico' => $anyo])
                ->andWhere(
                    [
                        'in',
                        'tipoEstudio_id',
                        [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID],
                    ]
                )->all();
        }
        $estudios = array_filter(
            $estudios,
            function ($estudio) use ($anyo, $dir_informes) {
                $ip = InformePublicado::find()->where(
                    [
                        'estudio_id' => $estudio->id,
                        'anyo' => $anyo,
                        'version' => InformePublicado::MAX_VERSION_INFORME,
                    ]
                )->one();
                if ($ip != null) {
                    $fichero_informe = "informe-es-{$estudio->id_nk}-v{$ip->getVersionMaxima()}.pdf";
                    return file_exists("{$dir_informes}/{$fichero_informe}");
                }

                return false;
            }
        );

        if (empty($estudios)) {
            throw new ServerErrorHttpException(
                sprintf(Yii::t('cati', 'No hay publicado ningún informe de evaluación del curso %d/%d.'), $anyo, $anyo + 1)
            );
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');

        return $this->renderPartial(
            'marc-xmls',
            [
                'anyo' => intval($anyo),
                'estudios' => $estudios,
            ]
        );
    }

    /**
     * Genera un MarcXML para exportar los informes de Doctorado de un año.
     *
     * Puede generar el MarcXML de un Programa de Doctorado dado o de todos.
     */
    public function actionMarcXmlDoct($anyo, $estudio_id = null)
    {
        Yii::$app->language = 'es';
        $dir_informes = Yii::getAlias('@webroot') . "/pdf/informes/{$anyo}";
        if ($estudio_id) {
            $estudios = [Estudio::getEstudio($estudio_id)];
        } else {
            $estudios = Estudio::find()
                ->where(['anyo_academico' => $anyo])
                ->andWhere(['tipoEstudio_id' => Estudio::DOCT_TIPO_ESTUDIO_ID])
                ->all();
        }

        $estudios = array_filter(
            $estudios,
            function ($estudio) use ($anyo, $dir_informes) {
                $ip = InformePublicado::find()->where(
                    [
                        'estudio_id' => $estudio->id,
                        'anyo' => $anyo,
                        'version' => InformePublicado::MAX_VERSION_INFORME_DOCT,
                    ]
                )->one();
                if ($ip != null) {
                    $fichero_informe = "informe-es-{$estudio->id_nk}-v{$ip->getVersionMaxima()}.pdf";
                    return file_exists("{$dir_informes}/{$fichero_informe}");
                }

                return false;
            }
        );

        if (empty($estudios)) {
            throw new ServerErrorHttpException(
                sprintf(Yii::t('cati', 'No hay publicado ningún informe de evaluación del curso %d/%d.'), $anyo, $anyo + 1)
            );
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');

        return $this->renderPartial(
            'doct/marc-xmls',
            [
                'anyo' => intval($anyo),
                'estudios' => $estudios,
            ]
        );
    }

    /**
     * Genera un MarcXML para exportar el ICED de un año.
     */
    public function actionMarcXmlIced($anyo)
    {
        Yii::$app->language = 'es';
        $informePublicado = InformePublicado::find()
            ->where(
                [
                    'estudio_id' => Estudio::ICED_ESTUDIO_ID,
                    'anyo' => $anyo,
                    'version' => InformePublicado::MAX_VERSION_INFORME_ICED,
                ]
            )->one();

        if (!$informePublicado) {
            throw new ServerErrorHttpException(
                sprintf(Yii::t('cati', 'No se ha publicado el ICED del curso %d/%d.'), $anyo, $anyo + 1)
            );
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');

        return $this->renderPartial(
            'iced/marc-xml',
            ['anyo' => intval($anyo)]
        );
    }

    /**
     * Lanzar la carga de los informes en Zaguán
     */
    public function actionCargarAZaguan($anyo, $tipo)
    {
        if ($tipo == 'grado-master') {
            $fichero = @fopen(Url::to(['informe/marc-xml', 'anyo' => $anyo], true), 'rb');
        } elseif ($tipo == 'doctorado') {
            $fichero = @fopen(Url::to(['informe/marc-xml-doct', 'anyo' => $anyo], true), 'rb');
        } elseif ($tipo == 'iced') {
            $fichero = @fopen(Url::to(['informe/marc-xml-iced', 'anyo' => $anyo], true), 'rb');
        } else {
            throw new ServerErrorHttpException(Yii::t('cati', 'Tipo de estudio desconocido.'));
        }
        if (!$fichero) {
            if ($tipo == 'iced') {
                throw new ServerErrorHttpException(
                    sprintf(Yii::t('cati', 'No se ha publicado el ICED del curso %d/%d.'), $anyo, $anyo + 1)
                );
            }
            throw new ServerErrorHttpException(sprintf(Yii::t('cati', 'No hay publicado ningún informe de evaluación del curso %d/%d.'), $anyo, $anyo + 1));
        }
        $contenido = stream_get_contents($fichero);
        fclose($fichero);

        $temp = tmpfile();
        fwrite($temp, $contenido);
        $meta_data = stream_get_meta_data($temp);
        $ruta = $meta_data['uri'];
        $cfile = new \CURLFile($ruta, 'application/xml');

        $wsUrl = 'https://desinvenio.unizar.es/batchuploader/robotupload';
        // Crear el recurso de cURL
        $curlHandle = curl_init();
        // Establecer las opciones
        curl_setopt_array(
            $curlHandle,
            [
                // TRUE para devolver el resultado de la transferencia como una cadena
                // del valor devuelto por curl_exec() en lugar de mostrarlo directamente.
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FAILONERROR => true,
                CURLOPT_URL => $wsUrl,
                // CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'zaguan_estudios',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => [
                    'file' => $cfile,
                    'mode' => '-ir',
                ],
            ]
        );
        // Enviar la petición y guardar la respuesta en la variable
        $respuesta = curl_exec($curlHandle);
        if (false === $respuesta) {
            throw new ServerErrorHttpException('Error: "' . curl_error($curlHandle) . '" - Cod: ' . curl_errno($curlHandle));
        }
        // Cerrar el recurso de curl para liberar recursos del sistema
        curl_close($curlHandle);
        fclose($temp);

        $nombre_usuario = Yii::$app->user->identity->username;
        $texto = "{$nombre_usuario} ha lanzado la carga a Zaguan de los informes de evaluación del año {$anyo}.";
        if ($tipo == 'iced') {
            $texto = "{$nombre_usuario} ha lanzado la carga a Zaguan del ICED del año {$anyo}.";
        }
        $mensaje = Yii::$app->mailer->compose()
            ->setFrom([Yii::$app->params['adminEmail'] => 'Robot Estudios'])
            ->setTo(Yii::$app->params['adminZaguan'])
            ->setSubject('Lanzada carga a Zaguan')
            ->setTextBody($texto);
        // ->setHtmlBody('<b>HTML content</b>');

        $mensaje->send();

        Yii::info($texto, 'gestion');

        return $this->render(
            '//gestion/zaguan-resultado',
            [
                'anyo' => $anyo,
                'respuesta' => $respuesta,
                'tipo' => $tipo,
            ]
        );
    }
}
