<?php
/**
 * /home/quique/Devel/cati/cati/runtime/giiant/49eb2de82346bc30092f584268252ed2.
 */

namespace app\controllers;

use app\models\Aecp;
use app\models\Agente;
use app\models\Centro;
use app\models\Estudio;
use app\models\Informacion;
use app\models\InformePublicado;
use app\models\Linea;
use app\models\Plan;
use app\models\PlanPublicado;
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
    public $freeAccessActions = ['calidad', 'lista', 'lista-ramas', 'lista-rama',
        'profesorado', 'resultados', 'ver', 'ver-doct', 'asignaturas', 'asignatura',
        'asignaturas-itinerario', 'asignaturas-en-otros-idiomas',
        'asignaturas-en-ingles', 'ultima-guia-publicada',
    ];

    /**
     * Lists alphabetically degrees of a given kind.
     *
     * @return mixed
     */
    public function actionLista($tipo_id)
    {
        $tipoEstudio = TipoEstudio::getTipoEstudio($tipo_id);

        $model = new Estudio();
        $estudios = $model->getEstudiosDelTipo($tipo_id);

        return $this->render('lista', [
            'estudios' => $estudios,
            'tipoEstudio' => $tipoEstudio,
        ]);
    }

    /**
     * Lists degrees of a given kind classified by branch of knowledge.
     *
     * @return mixed
     */
    public function actionListaRamas($tipo_id)
    {
        $tipoEstudio = TipoEstudio::getTipoEstudio($tipo_id);

        $model = new Estudio();
        $estudios = $model->getEstudiosPorRama($tipo_id);

        return $this->render('lista-ramas', [
            'estudios' => $estudios,
            'tipoEstudio' => $tipoEstudio,
        ]);
    }

    /**
     * Lists degrees of a given kind classified by kind of degree.
     *
     * @return mixed
     */
    public function actionListaRama($rama_id)
    {
        $rama = Rama::getRama($rama_id);

        $model = new Estudio();
        $estudios = $model->getEstudiosDeLaRama($rama_id);

        return $this->render('lista-rama', [
            'estudios' => $estudios,
            'rama' => $rama,
        ]);
    }

    /**
     * Displays main page about a Degree.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionVer($id)
    {
        $estudio = $this->findModel($id);
        // Desde el buscador de la página principal se va a este método
        // independientemente del tipo de estudio.
        if ($estudio->esDoctorado()) {
            return $this->redirect(['ver-doct', 'id' => $id], 301);
        }

        $planes_por_centro = $estudio->getPlanesPorCentro();
        $planes = $estudio->plans;
        $planes_activos = array_filter($planes, function ($plan) {
            return $plan->activo;
        });

        $informaciones = Informacion::find()
            ->innerJoinWith('seccion')->where(['estudio_id' => $id])->orderBy('pagina, orden')->all();
        $paginas = [];
        foreach ($informaciones as $info) {
            $paginas[$info->seccion->pagina][] = $info;
        }

        $a = new Agente();
        $coordinadores = $a->getCoordinadores($id);

        $anyo_academico = date('m') < 10 ? date('Y') - 1 : date('Y');
	$anyo_academico += 1;  // Para que Angeles Portoles prueba la carga de asignaturas en formación
        $anterior_anyo_academico = $anyo_academico - 1;
        $language = Yii::$app->language;
        $informe_publicado = InformePublicado::find()
            ->where(['estudio_id' => $id, 'anyo' => $anterior_anyo_academico, 'language' => $language])->one();
        $version_informe = isset($informe_publicado) ? $informe_publicado->version : 0;
        $plan_publicado = PlanPublicado::find()
            ->where(['estudio_id' => $id, 'anyo' => $anterior_anyo_academico, 'language' => $language])->one();
        $version_plan = isset($plan_publicado) ? $plan_publicado->version : 0;

        return $this->render('ver', [
            'anyo_academico' => $anyo_academico,
            'coordinadores' => $coordinadores,
            'estudio' => $estudio,
            'planes' => $planes_activos,
            'planes_por_centro' => $planes_por_centro,
            'paginas' => $paginas,
            'version_informe' => $version_informe,
            'version_plan' => $version_plan,
        ]);
    }

    public function actionVerDoct($id)
    {
        if (!$id) {
            return $this->redirect(Url::home(true));
        }
        $estudio = $this->findModel($id);
        $planes_por_centro = $estudio->getPlanesPorCentro();

        $informaciones = Informacion::find()
            ->innerJoinWith('seccion')->where(['estudio_id' => $id])->orderBy('pagina, orden')->all();
        $paginas = [];
        foreach ($informaciones as $info) {
            $paginas[$info->seccion->pagina][] = $info;
        }

        $lineas = Linea::find()->where(['estudio_id' => $id])->all();

        // Anterior año académico
        $anyo_academico = date('m') < 10 ? date('Y') - 2 : date('Y') - 1;
        $language = Yii::$app->language;
        $informe_publicado = InformePublicado::find()
            ->where(['estudio_id' => $id, 'anyo' => $anyo_academico, 'language' => $language])->one();
        $version_informe = isset($informe_publicado) ? $informe_publicado->version : 0;
        $plan_publicado = PlanPublicado::find()
            ->where(['estudio_id' => $id, 'anyo' => $anyo_academico, 'language' => $language])->one();
        $version_plan = isset($plan_publicado) ? $plan_publicado->version : 0;

        return $this->render('ver-doct', [
            'anyo_academico' => $anyo_academico,
            'estudio' => $estudio,
            'lineas' => $lineas,
            'paginas' => $paginas,
            'planes_por_centro' => $planes_por_centro,
            'version_informe' => $version_informe,
            'version_plan' => $version_plan,
        ]);
    }

    /**
     * Muestra la información de calidad de una titulación.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionCalidad($id)
    {
        $estudio = $this->findModel($id);

        $anyo_academico = date('m') < 10 ? date('Y') - 2 : date('Y') - 1;
        $language = Yii::$app->request->cookies->getValue('language', 'es');
        $informe_publicado = InformePublicado::find()
            ->where(['estudio_id' => $id, 'anyo' => $anyo_academico, 'language' => $language])->one();
        $version_informe = isset($informe_publicado) ? $informe_publicado->version : 0;
        $plan_publicado = PlanPublicado::find()
            ->where(['estudio_id' => $id, 'anyo' => $anyo_academico, 'language' => $language])->one();
        $version_plan = isset($plan_publicado) ? $plan_publicado->version : 0;

        return $this->render('calidad', [
            'estudio' => $estudio,
            'version_informe' => $version_informe,
            'version_plan' => $version_plan,
        ]);
    }

    /**
     * Muestra las encuestas y resultados académicos de un estudio.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionResultados($id)
    {
        $estudio = $this->findModel($id);
        $planes = $estudio->plans;
        $planes_activos = array_filter($planes, function ($plan) {
            return $plan->activo;
        });

        return $this->render('resultados', [
            'estudio' => $estudio,
            'planes' => $planes_activos,
        ]);
    }

    /**
     * Muestra el profesorado de un estudio.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionProfesorado($id)
    {
        $estudio = $this->findModel($id);

        return $this->render('profesorado', [
            'estudio' => $estudio,
        ]);
    }

    /**
     * Muestra la lista de asignaturas de un plan de un estudio y centro.
     */
    public function actionAsignaturas($anyo_academico, $estudio_id, $centro_id, $plan_id_nk)
    {
        $estudio = $this->findModel($estudio_id);

        $plan = Plan::findOne(['id_nk' => $plan_id_nk, 'estudio_id' => $estudio_id, 'centro_id' => $centro_id]);
        if (!$plan) {
            throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese plan.  ☹'));
        }
        $centro = Centro::findOne(['id' => $centro_id]);

        $aecp = new Aecp();
        $asignaturas = $aecp->getAsignaturas($estudio_id, $centro_id, $plan_id_nk, $anyo_academico);
        $itinerarios = $aecp->getItinerarios($centro_id, $plan_id_nk, $anyo_academico);

        return $this->render('asignaturas', [
            'estudio' => $estudio,
            'centro' => $centro,
            'plan' => $plan,
            'asignaturas' => $asignaturas,
            'itinerarios' => $itinerarios,
            'anyo_academico' => intval($anyo_academico),
        ]);
    }

    /**
     * Muestra la información de una asignatura y año.
     */
    public function actionAsignatura($anyo_academico, $asignatura_id, $estudio_id, $centro_id, $plan_id_nk)
    {
        $estudio = $this->findModel($estudio_id);

        $plan = Plan::findOne(['id_nk' => $plan_id_nk, 'estudio_id' => $estudio_id, 'centro_id' => $centro_id]);
        if (!$plan) {
            throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese plan en ese centro.  ☹'));
        }
        $centro = Centro::findOne(['id' => $centro_id]);

        $aecp = new Aecp();
        $asignatura = $aecp->getAsignatura($asignatura_id, $estudio_id, $centro_id, $plan_id_nk, $anyo_academico);
        if (!$asignatura) {
            throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado esa asignatura.  ☹'));
        }
        $asignatura2 = $asignatura['asignatura'];
        $profesores = $asignatura['profesores'];

        return $this->render('asignatura', [
            'anyo_academico' => intval($anyo_academico),
            'asignatura' => $asignatura2,
            'estudio' => $estudio,
            'centro' => $centro,
            'plan' => $plan,
            'profesores' => $profesores,
            'urlGuias' => Yii::$app->params['urlGuias'],
        ]);
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
        $nombre_itinerario = $aecp->getNombreItinerario($itinerario_id_nk, $anyo_academico);

        return $this->render('asignaturas-itinerario', [
            'estudio' => $estudio,
            'centro' => $centro,
            'plan' => $plan,
            'asignaturas' => $asignaturas,
            'nombre_itinerario' => $nombre_itinerario,
            'anyo_academico' => intval($anyo_academico),
        ]);
    }

    /**
     * Muestra la última guía publicada de una asignatura.
     *
     * Solicitado por David Charro para enlazar desde el sitio Drupal corporativo.
     * Vg: http://diec2.unizar.es/personal/francisco-javier-mateo-gascon
     * Esta función podrá borrarse si pasan a enlazar directamente a Sigm@.
     */
    public function actionUltimaGuiaPublicada($asignatura_id)
    {
        $anyo_academico = date('m') < 6 ? date('Y') - 1 : date('Y');
        $language = Yii::$app->request->cookies->getValue('language', 'es');
        $file = "${asignatura_id}_$language.pdf";

        $pdfdir = Yii::getAlias('@webroot').'/pdf/guias/'.$anyo_academico;
        $pdfdirurl = Url::base().'/pdf/guias/'.$anyo_academico;
        $hay_guia_actual = file_exists("$pdfdir/$file");

        $pdfdiranterior = Yii::getAlias('@webroot').'/pdf/guias/'.($anyo_academico - 1);
        $pdfdiranteriorurl = Url::base().'/pdf/guias/'.($anyo_academico - 1);
        $hay_guia_anterior = file_exists("$pdfdiranterior/$file");

        if ($hay_guia_actual) {
            return $this->redirect("$pdfdirurl/$file");
        }
        if ($hay_guia_anterior) {
            return $this->redirect("$pdfdiranteriorurl/$file");
        }
        throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado la guía docente de esa asignatura.  ☹'));
    }

    /**
     * Muestra las asignaturas que se imparten en otros idiomas.
     *
     * Solicitado por David Charro/GIC para enlazar desde el sitio Drupal corporativo
     */
    public function actionAsignaturasEnOtrosIdiomas()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $anyo_academico = date('m') < 6 ? date('Y') - 1 : date('Y');
        $aecp = new Aecp();
        $asignaturas = $aecp->getEnOtrosIdiomas($anyo_academico);

        return ['asignaturas' => $asignaturas];
    }

    /**
     * Muestra las asignaturas que se imparten en inglés.
     *
     * Solicitado por David Charro/GIC para enlazar desde el sitio Drupal corporativo
     */
    public function actionAsignaturasEnIngles()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $anyo_academico = date('m') < 6 ? date('Y') - 1 : date('Y');
        $aecp = new Aecp();
        $asignaturas = $aecp->getEnIngles($anyo_academico);

        return ['asignaturas' => $asignaturas];
    }
}
