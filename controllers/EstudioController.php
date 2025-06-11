<?php
/**
 * /home/quique/Devel/cati/cati/runtime/giiant/49eb2de82346bc30092f584268252ed2.
 */

namespace app\controllers;

use app\models\Aecp;
use app\models\Agente;
use app\models\AsignaturaFicha;
use app\models\Calendario;
use app\models\Centro;
use app\models\Doctorado;
use app\models\Equipo;
use app\models\Estudio;
use app\models\Informacion;
use app\models\InformePublicado;
use app\models\Linea;
use app\models\NotasPlan;
use app\models\Plan;
use app\models\PlanPublicado;
use app\models\Profesorado;
use app\models\Rama;
use app\models\TipoEstudio;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * This is the class for controller "EstudioController".
 */
class EstudioController extends \app\controllers\base\EstudioController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(
            parent::behaviors(), [
                'access' => [
                    'rules' => [
                        [
                            'actions' => ['lista', 'lista-ramas', 'lista-rama', 'listado-estudios',
                                'profesorado', 'resultados', 'ver', 'ver-doct', 'asignaturas', 'asignatura',
                                'asignaturas-itinerario', 'asignaturas-en-otros-idiomas',
                                'asignaturas-in-english', 'asignaturas-en-ingles', 'ultima-guia-publicada',
                            ],
                            'allow' => true,
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists alphabetically degrees of a given kind.
     *
     * @return mixed
     */
    public function actionLista($tipo_id, $anyo_academico = null)
    {
        if (!$anyo_academico) {
            $anyo_academico = $tipo_id == Estudio::DOCT_TIPO_ESTUDIO_ID ? Calendario::getAnyoDoctorado() : Calendario::getAnyoAcademico();
        }
        $tipoEstudio = TipoEstudio::getTipoEstudio($tipo_id);
        $estudios = Estudio::getEstudiosDelTipo($anyo_academico, $tipo_id);

        return $this->render(
            'lista', [
                'anyo' => $anyo_academico,
                'estudios' => $estudios,
                'tipoEstudio' => $tipoEstudio,
            ]
        );
    }

    /**
     * Lists degrees of a given kind classified by branch of knowledge.
     *
     * @return mixed
     */
    public function actionListaRamas($tipo_id, $anyo_academico = null)
    {
        if (!$anyo_academico) {
            $anyo_academico = $tipo_id == Estudio::DOCT_TIPO_ESTUDIO_ID ? Calendario::getAnyoDoctorado() : Calendario::getAnyoAcademico();
        }
        $tipoEstudio = TipoEstudio::getTipoEstudio($tipo_id);
        $estudios = Estudio::getEstudiosPorRama($anyo_academico, $tipo_id);

        return $this->render(
            'lista-ramas', [
                'anyo' => $anyo_academico,
                'estudios' => $estudios,
                'tipoEstudio' => $tipoEstudio,
            ]
        );
    }

    /**
     * Lists degrees of a given kind classified by kind of degree.
     *
     * @return mixed
     */
    public function actionListaRama($rama_id, $anyo_academico = null)
    {
        if (!$anyo_academico) {
            $anyo_academico = Calendario::getAnyoAcademico();
        }
        $rama = Rama::getRama($rama_id);
        $estudios = Estudio::getEstudiosDeLaRama($anyo_academico, $rama_id);

        return $this->render(
            'lista-rama', [
                'anyo' => $anyo_academico,
                'estudios' => $estudios,
                'rama' => $rama,
            ]
        );
    }

    /**
     * Muestra la página principal de un estudio.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionVer($id, $anyo_academico = null)
    {
        $anyo_academico = $anyo_academico ?: Calendario::getAnyoAcademico();
        $es_curso_actual = $anyo_academico == Calendario::getAnyoAcademico();
        $estudio = Estudio::getEstudioByNk($anyo_academico, $id);

        // Desde el buscador de la página principal se va a este método
        // independientemente del tipo de estudio.
        if ($estudio->esDoctorado()) {
            $anyo_academico = $anyo_academico ?: Calendario::getAnyoDoctorado();

            return $this->redirect(['ver-doct', 'id' => $id, 'anyo_academico' => $anyo_academico], 301);
        }

        $planes_por_centro = $estudio->getPlanesPorCentro();
        $planes = $estudio->plans;
        $planes_activos = array_filter(
            $planes, function ($plan) {
                return $plan->activo;
            }
        );

        $informaciones = Informacion::find()
            ->innerJoinWith('seccion')->where(['estudio_id_nk' => $id])->orderBy('pagina, orden')->all();
        $paginas = [];
        foreach ($informaciones as $info) {
            $paginas[$info->seccion->pagina][] = $info;
        }

        $coordinadores = Agente::getCoordinadores($estudio->id);

        $anterior_anyo_academico = $anyo_academico - 1;
        $language = Yii::$app->language;
        $informe_publicado = InformePublicado::find()
            ->where(['estudio_id_nk' => $estudio->id_nk, 'anyo' => $anterior_anyo_academico, 'language' => $language])->one();
        $version_informe = isset($informe_publicado) ? $informe_publicado->version : 0;
        $plan_publicado = PlanPublicado::find()
            ->where(['estudio_id_nk' => $estudio->id_nk, 'anyo' => $anterior_anyo_academico, 'language' => $language])->one();
        $version_plan = isset($plan_publicado) ? $plan_publicado->version : 0;

        return $this->render(
            'ver', [
                'anyo_academico' => intval($anyo_academico),
                'anyo_profesorado' => Profesorado::getAnyoUltimaEstructura($anyo_academico, $id),
                'coordinadores' => $coordinadores,
                'es_curso_actual' => $es_curso_actual,
                'estudio' => $estudio,
                'planes' => $planes_activos,
                'planes_por_centro' => $planes_por_centro,
                'paginas' => $paginas,
                'version_informe' => $version_informe,
                'version_plan' => $version_plan,
            ]
        );
    }

    public function actionVerDoct($id, $anyo_academico = null)
    {
        $anyo_academico = $anyo_academico ?: Calendario::getAnyoDoctorado();
        $es_curso_actual = $anyo_academico == Calendario::getAnyoAcademico();
        $estudio = Estudio::getEstudioByNk($anyo_academico, $id);

        $planes_por_centro = $estudio->getPlanesPorCentro();

        $informaciones = Informacion::find()
            ->innerJoinWith('seccion')->where(['estudio_id_nk' => $id])->orderBy('pagina, orden')->all();
        $paginas = [];
        foreach ($informaciones as $info) {
            $paginas[$info->seccion->pagina][] = $info;
        }

        $datos = Doctorado::find()
            ->where(['cod_estudio' => $estudio->id_nk])
            ->orderBy(['ano_academico' => SORT_DESC])
            ->limit(1)->one();

        $lineas = Linea::find()->where(['estudio_id' => $estudio->id])->all();
        $nombres_equipos = Equipo::getNombresEquipos($estudio->id);
        $miembros_equipos = Equipo::getMiembrosEquipos($estudio->id);

        $anterior_anyo_academico = $anyo_academico - 1;
        $language = Yii::$app->language;
        $informe_publicado = InformePublicado::find()
            ->where(['estudio_id_nk' => $estudio->id_nk, 'anyo' => $anterior_anyo_academico, 'language' => $language])->one();
        $version_informe = isset($informe_publicado) ? $informe_publicado->version : 0;

        $plan_publicado = PlanPublicado::find()
            ->where(['estudio_id_nk' => $estudio->id_nk, 'anyo' => $anterior_anyo_academico, 'language' => $language])->one();
        $version_plan = isset($plan_publicado) ? $plan_publicado->version : 0;

        return $this->render(
            'ver-doct', [
                'anyo_academico' => intval($anyo_academico),
                'datos' => $datos,
                'es_curso_actual' => $es_curso_actual,
                'estudio' => $estudio,
                'lineas' => $lineas,
                'miembros_equipos' => $miembros_equipos,
                'nombres_equipos' => $nombres_equipos,
                'paginas' => $paginas,
                'planes_por_centro' => $planes_por_centro,
                'version_informe' => $version_informe,
                'version_plan' => $version_plan,
            ]
        );
    }

    /**
     * Muestra la lista de asignaturas de un plan de un estudio, centro y año.
     */
    public function actionAsignaturas($anyo_academico, $estudio_id, $centro_id, $plan_id_nk)
    {
        $estudio = $this->findModel($estudio_id);

        if (in_array($estudio->id_nk, Estudio::FALSOS_ESTUDIO_IDS)) {
            $centro_id = Estudio::CENTROS_PROGRAMAS_CONJUNTOS[$estudio->id_nk];
        }

        $plan = Plan::findOne(['id_nk' => $plan_id_nk, 'estudio_id' => $estudio_id, 'centro_id' => $centro_id]);
        if (!$plan) {
            throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese plan.  ☹'));
        }
        $centros = Centro::find()->where(['id' => $centro_id])->all();

        $aecp = new Aecp();
        $asignaturas = $aecp->getAsignaturas($estudio_id, $centro_id, $plan_id_nk, $anyo_academico);
        $itinerarios = $aecp->getItinerarios($centro_id, $plan_id_nk, $anyo_academico);

        $notas = NotasPlan::getNotasPlan($plan_id_nk);

        return $this->render(
            'asignaturas', [
                'anyo_academico' => intval($anyo_academico),
                'asignaturas' => $asignaturas,
                'centros' => $centros,
                'es_curso_actual' => $anyo_academico == Calendario::getAnyoAcademico(),
                'estudio' => $estudio,
                'itinerarios' => $itinerarios,
                'notas' => $notas,
                'plan' => $plan,
            ]
        );
    }

    /**
     * Muestra la información de una asignatura y año.
     */
    public function actionAsignatura($anyo_academico, $asignatura_id, $estudio_id, $centro_id, $plan_id_nk)
    {
        $estudio = $this->findModel($estudio_id);
        $centro = Centro::findOne(['id' => $centro_id]);

        if (in_array($estudio->id_nk, Estudio::FALSOS_ESTUDIO_IDS)) {
            $centro_id = Estudio::CENTROS_PROGRAMAS_CONJUNTOS[$estudio->id_nk];
        }

        $plan = Plan::findOne(['id_nk' => $plan_id_nk, 'estudio_id' => $estudio_id, 'centro_id' => $centro_id]);
        if (!$plan) {
            throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese plan en ese centro.  ☹'));
        }

        $aecp = new Aecp();
        $asignatura = $aecp->getAsignatura($asignatura_id, $estudio_id, $centro_id, $plan_id_nk, $anyo_academico);
        if (!$asignatura) {
            throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado esa asignatura.  ☹'));
        }
        $asignatura2 = $asignatura['asignatura'];
        /* Los datos de los profesores están en la tabla `aecp_profesor`,
           que rellena la pasarela `ods_aecp.ktr` a partir de la tabla ` ODS_ASIG_EST_CENT_PLAN_PROFES` del ODS. */
        $profesores = $asignatura['profesores'];
        $tiene_ficha = AsignaturaFicha::find()->where(['anyo_academico' => $anyo_academico, 'asignatura_id' => $asignatura_id, 'language' => Yii::$app->language])->count();

        return $this->render(
            'asignatura', [
                'anyo_academico' => intval($anyo_academico),
                'asignatura' => $asignatura2,
                'estudio' => $estudio,
                'centro' => $centro,
                'plan' => $plan,
                'profesores' => $profesores,
                'tiene_ficha' => $tiene_ficha,
                'urlGuias' => Yii::$app->params['urlGuias'],
            ]
        );
    }

    /**
     * Muestra la lista de asignaturas de un itinerario y año.
     */
    public function actionAsignaturasItinerario($anyo_academico, $estudio_id, $centro_id, $plan_id_nk, $itinerario_id_nk)
    {
        $estudio = $this->findModel($estudio_id);

        $plan = Plan::findOne(['id_nk' => $plan_id_nk, 'estudio_id' => $estudio_id, 'centro_id' => $centro_id]);
        if (!$plan) {
            throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese plan.  ☹'));
        }
        $centro = Centro::findOne(['id' => $centro_id]);

        $aecp = new Aecp();
        $asignaturas = $aecp->getAsignaturasItinerario(
            $estudio_id,
            $centro_id,
            $plan_id_nk,
            $itinerario_id_nk,
            $anyo_academico
        );
        $itinerarios = $aecp->getItinerarios($centro_id, $plan_id_nk, $anyo_academico);
        $nombre_itinerario = $aecp->getNombreItinerario($itinerario_id_nk, $anyo_academico);

        return $this->render(
            'asignaturas-itinerario', [
                'estudio' => $estudio,
                'centro' => $centro,
                'plan' => $plan,
                'asignaturas' => $asignaturas,
                'nombre_itinerario' => $nombre_itinerario,
                'itinerarios' => $itinerarios,
                'anyo_academico' => intval($anyo_academico),
            ]
        );
    }

    /**
     * Muestra la última guía publicada de una asignatura.
     *
     * Solicitado por David Charro para enlazar desde el sitio Drupal corporativo.
     * Vg: http://diec2.unizar.es/personal/francisco-javier-mateo-gascon
     * Esta función podrá borrarse si pasan a enlazar directamente a Sigm@.
     */
    /*
    public function actionUltimaGuiaPublicada($asignatura_id)
    {
        $anyo_academico = date('m') < 6 ? date('Y') - 1 : date('Y');
        $language = Yii::$app->request->cookies->getValue('language', 'es');
        $file = "{$asignatura_id}_{$language}.pdf";

        $pdfdir = Yii::getAlias('@webroot') . '/pdf/guias/' . $anyo_academico;
        $pdfdirurl = Url::base() . '/pdf/guias/' . $anyo_academico;
        $hay_guia_actual = file_exists("{$pdfdir}/{$file}");

        $pdfdiranterior = Yii::getAlias('@webroot') . '/pdf/guias/' . ($anyo_academico - 1);
        $pdfdiranteriorurl = Url::base() . '/pdf/guias/' . ($anyo_academico - 1);
        $hay_guia_anterior = file_exists("{$pdfdiranterior}/{$file}");

        if ($hay_guia_actual) {
            return $this->redirect("{$pdfdirurl}/{$file}");
        }
        if ($hay_guia_anterior) {
            return $this->redirect("{$pdfdiranteriorurl}/{$file}");
        }
        throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado la guía docente de esa asignatura.  ☹'));
    }
    */

    /**
     * Muestra las asignaturas que se imparten en otros idiomas.
     *
     * Solicitado por David Charro/GIC para enlazar desde el sitio Drupal corporativo
     */
    public function actionAsignaturasEnOtrosIdiomas()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $anyo_academico = Calendario::getAnyoAcademico();
        $asignaturas = Aecp::getEnOtrosIdiomas($anyo_academico);

        return ['asignaturas' => $asignaturas];
    }

    /**
     * Devuelve las asignaturas que se imparten en inglés, con descripciones en inglés.
     *
     * Solicitado por David Charro/GIC para enlazar desde el sitio Drupal corporativo
     */
    public function actionAsignaturasInEnglish()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $anyo_academico = Calendario::getAnyoAcademico();
        $asignaturas = Aecp::getInEnglish($anyo_academico);

        return ['asignaturas' => $asignaturas];
    }

    /**
     * Devuelve las asignaturas que se imparten en inglés, con descripciones en castellano.
     *
     * Solicitado por David Charro/GIC para enlazar desde el sitio Drupal corporativo
     */
    public function actionAsignaturasEnIngles()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $anyo_academico = Calendario::getAnyoAcademico();
        $asignaturas = Aecp::getEnIngles($anyo_academico);

        return ['asignaturas' => $asignaturas];
    }

    /**
     * Devuelve los estudios, para el buscador de la web principal de la Universidad.
     *
     * Solicitado por Bea Millán/GIC para enlazar desde el sitio Drupal corporativo
     */
    public function actionListadoEstudios()
    {
        $estudios = Estudio::getEstudiosActivos();

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return ['estudios' => $estudios];
    }
}
