<?php
/**
 * Controlador de la zona autenticada.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */

namespace app\controllers;

use app\controllers\base\CatiController;
use app\models\AcreditacionEstudio;
use app\models\Aecp;
use app\models\Agente;
use app\models\Calendario;
use app\models\Centro;
use app\models\Estudio;
use app\models\InformePregunta;
use app\models\InformePublicado;
use app\models\InformeRespuesta;
use app\models\Plan;
use app\models\PlanPregunta;
use app\models\PlanPublicado;
use app\models\PlanRespuesta;
use app\models\Profesorado;
use app\models\UploadPdf;
use app\models\User;
use Yii;
use yii\db\Expression;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use Cocur\BackgroundProcess\BackgroundProcess;

// based on original work from the PHP Laravel framework
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}


class GestionController extends CatiController
{
    public function behaviors()
    {
        $request = Yii::$app->request;
        /*
        if ($request->isPost && !(
                str_contains($request->url, 'guardar-centro-acreditacion')
                || str_contains($request->url, 'actualizar-encuestas')
                || str_contains($request->url, 'actualizar-datos-academicos')
                || str_contains($request->url, 'guardar-periodo-evaluacion')
                || str_contains($request->url, 'subir-procedimiento')
            )) {
            $id = $request->post('Plan')['id'];
        } else {
            // GET, HEAD, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH.
            $id = $request->get('id');
            $centro_id = $request->get('centro_id');
        }
        */
        $id = $request->post('Plan')['id'] ?? null;
        if (!$id) $id = $request->get('id');
        $centro_id = $request->get('centro_id');

        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                // 'ruleConfig' => ['class' => \app\rbac\CoordinadorEstudioRule::className()],
                // 'only' => ['calidad', 'index'],
                'rules' => [
                    [
                        'actions' => [
                            'calidad', 'grado-master', 'doctorado',
                            'ver-agentes',
                            'ver-coordinadores', 'ver-correos-coordinadores',
                            'ver-presidentes', 'ver-correos-presidentes',
                            'ver-correos-expertos',
                            'ver-encuestas',
                            'ver-estructura',
                            'ver-horarios',
                            'ver-resultados-academicos',
                            'ver-url-web-plan', 'actualizar-url-web-plan', 'guardar-url-web-plan', 'ver-webs-especificas',
                            'ver-centros-acreditacion', 'actualizar-centro-acreditacion', 'guardar-centro-acreditacion', 'ver-centro-acreditacion',
                            'ver-acreditacion-estudios', 'actualizar-acreditacion-estudio', 'guardar-acreditacion-estudio', 'ver-acreditacion-estudio',
                            'ver-periodos-evaluacion', 'actualizar-periodo-evaluacion', 'guardar-periodo-evaluacion', 'ver-periodo-evaluacion',
                            'evolucion-profesorado', 'lista-evolucion-profesorado',
                            'lista-informes', 'lista-informes-version',
                            'abrir-informe', 'cerrar-informe',
                            'seleccionar-pregunta', 'extractos',
                            'lista-planes',
                            'abrir-plan', 'cerrar-plan',
                            'seleccionar-pregunta-plan', 'extractos-plan',
                            'seleccionar-centro-paim',
                            'lista-informaciones',
                            'lista-notas-planes',
                            // 'guias-docentes',
                            'lista-indicadores', 'subir-indicadores',
                            'subir-procedimiento',
                            'cargar-a-zaguan',
                            'actualizar-datos-academicos',
                            'actualizar-datos-doctorado',
                            'actualizar-encuestas',
                            'cargar-url-horarios-anterior',
                            'cargar-webs-especificas-anterior',
                        ],
                        'allow' => true,
                        'roles' => ['gestor'],
                    ], [
                        'actions' => ['index', 'mis-estudios'],
                        'allow' => true,
                        'roles' => ['@'],
                    ], [
                        'actions' => ['ver-horario', 'actualizar-horario', 'guardar-horario',
                            'ver-url-web-plan', 'actualizar-url-web-plan', 'guardar-url-web-plan', ],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) use ($id) {
                            $plan = Plan::findOne(['id' => $id]);
                            if (!$plan) {
                                return false;
                            }
                            $estudio = $plan->estudio;

                            return Yii::$app->user->can('editarInforme', ['estudio' => $estudio]);
                        },
                        'roles' => ['@'],
                    ], [
                        'actions' => ['extractos-paim-centro'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) use ($centro_id) {
                                return Yii::$app->user->can('verExtractosPaimCentro', ['centro' => Centro::getCentro($centro_id)]);
                            },
                        'roles' => ['@'],
                    ], [
                        'actions' => ['comision-doctorado'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->esComisionDoctorado();
                        },
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    /**
     * Página de aterrizaje tras iniciar sesión.
     */
    public function actionIndex()
    {
        $usuario = Yii::$app->user;
        if ($usuario->can('unidadCalidad')) {
            return $this->redirect(['//gestion/calidad']);
        } elseif ($usuario->can('gradoMaster')) {
            return $this->redirect(['//gestion/grado-master']);
        } elseif ($usuario->can('escuelaDoctorado')) {
            return $this->redirect(['//gestion/doctorado']);
        } else {
            return $this->redirect(['//gestion/mis-estudios']);
        }
    }

    /**
     * Página para los gestores de la Unidad de Calidad
     */
    public function actionCalidad()
    {
        return $this->render('calidad');
    }

    /**
     * Página para los gestores de la sección de Grado y Máster
     */
    public function actionGradoMaster()
    {
        return $this->render('grado-master');
    }

    /**
     * Página para los gestores de la Escuela de Doctorado
     */
    public function actionDoctorado()
    {
        return $this->render('doctorado');
    }

    /**
     * Página para la Comisión de Doctorado
     */
    public function actionComisionDoctorado()
    {
        $anyo = Calendario::getAnyoDoctorado() - 1;
        $language = Yii::$app->language;
        $e = new Estudio();
        $datos = $e->getListadoPlanes($anyo, $language, 'doctorado');

        return $this->render(
            'comision-doctorado', ['anyo' => $anyo, 'datos' => $datos, 'tipo' => 'doctorado']
        );
    }

    /**
     * Página para los usuarios no-gestores
     */
    public function actionMisEstudios()
    {
        $identidad = Yii::$app->user->identity;

        $idNkEstudiosCoordinados = Agente::getIdNkEstudiosCoordinados($identidad->username);
        $idNkEstudiosPresididos = Agente::getIdNkEstudiosPresididos($identidad->username);
        $idNkEstudios = array_unique(array_merge($idNkEstudiosCoordinados, $idNkEstudiosPresididos));
        $estudios = array_filter(array_map(['\app\models\Estudio', 'getUltimoEstudioByNk'], $idNkEstudios));

        $idNkPlanes = Agente::getIdNkPlanesCoorOPresi($identidad->username);
        $planes = array_filter(array_map(['\app\models\Plan', 'getUltimoPlanByNk'], $idNkPlanes));

        $planesPorEstudio = [];
        foreach ($planes as $plan) {
            $planesPorEstudio[$plan->estudio_id][] = $plan;
        }

        $centros_dirigidos = Centro::find()->where(['nip_decano' => $identidad->username])->all();

        return $this->render(
            'mis-estudios',
            [
                'centros_dirigidos' => $centros_dirigidos,
                'estudios' => $estudios,
                'idNkEstudiosCoordinados' => $idNkEstudiosCoordinados,
                'planes' => $planesPorEstudio,
            ]
        );
    }

    /**
     * Muestra un listado de las titulaciones de grado y máster,
     * con enlaces a la lista de agentes de cada titulación.
     */
    public function actionVerAgentes()
    {
        $anyo_academico = Calendario::getAnyoAcademico();
        $estudios = Estudio::find()
            ->where(['anyo_academico' => $anyo_academico])
            ->andWhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]])
            ->andWhere(['activo' => 1])
            ->all();

        return $this->render(
            'ver-agentes',
            ['estudios' => $estudios]
        );
    }

    /**
     * Muestra un listado de los planes de grado y máster,
     * con el nombre y dirección de correo del coordinador de cada plan.
     */
    public function actionVerCoordinadores($anyo_academico = null)
    {
        if (!$anyo_academico) {
            $anyo_academico = Calendario::getAnyoAcademico();
        }
        $query = Plan::find()
            ->innerJoinWith('estudio')
            ->where(['estudio.anyo_academico' => $anyo_academico])
            ->andWhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]]);
        // die($query->createCommand()->getRawSql());  // DEBUG
        $planes = $query->all();

        foreach ($planes as $plan) {
            $coordinadores = Agente::getCoordinadores($plan->estudio_id);

            $plan['nombre_coordinador'] = $coordinadores[$plan->centro_id]['nombre_completo'] ?? $plan->nombre_coordinador;
            $plan['email_coordinador'] =  $coordinadores[$plan->centro_id]['email'] ?? $plan->email_coordinador;
        }
        return $this->render(
            'ver-coordinadores',
            ['planes' => $planes]
        );
    }

    /**
     * Muestra las direcciones de correo de todos los coordinadores de planes
     * de Grado y Máster.
     */
    public function actionVerCorreosCoordinadores($anyo_academico = null)
    {
        if (!$anyo_academico) {
            $anyo_academico = Calendario::getAnyoAcademico();
        }
        $planes = Plan::find()
            ->innerJoinWith('estudio')
            ->where(['estudio.anyo_academico' => $anyo_academico])
            ->andWhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]])
            ->andWhere(['is not', 'plan.email_coordinador', null])
            ->orderBy('email_coordinador')->all();

        foreach ($planes as $plan) {
            $coordinadores = Agente::getCoordinadores($plan->estudio_id);

            $plan['nombre_coordinador'] = $coordinadores[$plan->centro_id]['nombre_completo'] ?? $plan->nombre_coordinador;
            $plan['email_coordinador'] =  $coordinadores[$plan->centro_id]['email'] ?? $plan->email_coordinador;
        }

        $coordinadores = array_unique(
            array_map(
                function ($plan) {
                    $nombre = $plan->nombre_coordinador;
                    $email = filter_var($plan->email_coordinador, FILTER_VALIDATE_EMAIL) ? $plan->email_coordinador : 'FIXME';

                    return sprintf('"%s" &lt;%s&gt;', $nombre, $email);
                },
                $planes
            )
        );

        return $this->render(
            'ver-correos-coordinadores',
            ['coordinadores' => $coordinadores]
        );
    }


    /**
     * Muestra las direcciones de correo de todos los expertos del rector.
     */
    public function actionVerCorreosExpertos()
    {
        $expertos = Agente::find()
            // ->where(['like', 'rol', 'Expert'])  // %Expert% => Del centro y del rector
            // ->where(['like', 'rol', 'Expert%rector', false])  // Expert%rector
            ->where(new \yii\db\Expression("rol LIKE 'Expert%rector'"))
            ->orderBy('email', 'nombre')
            ->all();
        $direcciones = array_unique(
            array_map(
                function ($experto) {
                    return "'{$experto->nombre} {$experto->apellido1} {$experto->apellido2}' &lt;{$experto->email}&gt;";
                },
                $expertos
            )
        );

        return $this->render(
            'ver-correos-expertos',
            ['expertos' => $direcciones]
        );
    }


    /**
     * Muestra un listado de los planes de grado y máster,
     * con el nombre y dirección de correo del presidente de la comisión de garantía de cada plan.
     */
    public function actionVerPresidentes()
    {
        $language = Yii::$app->request->cookies->getValue('language', 'es');
        $a = new Agente();
        $presidentes = $a->getPresidentes($language);

        return $this->render(
            'ver-presidentes',
            ['presidentes' => $presidentes]
        );
    }

    /**
     * Muestra las direcciones de correo de todos los presidentes de las Comisiones de Garantía de la Calidad.
     */
    public function actionVerCorreosPresidentes()
    {
        $presidentes = Agente::find()
            ->where(['agente.comision_id' => 'G'])
            ->andWhere(['agente.rol' => ['Presidente', 'Presidenta']])
            ->orderBy('email')
            ->all();
        $direcciones = array_unique(
            array_map(
                function ($presidente) {
                    return "'{$presidente->nombre} {$presidente->apellido1} {$presidente->apellido2}' &lt;{$presidente->email}&gt;";
                },
                $presidentes
            )
        );

        return $this->render(
            'ver-correos-presidentes',
            ['presidentes' => $direcciones]
        );
    }


    public function actionVerHorario($id)
    {
        $plan = Plan::getPlan($id);

        return $this->render(
            'ver-horario',
            ['plan' => $plan]
        );
    }

    /**
     * Ver los datos de acreditación de un estudio.
     */
    public function actionVerAcreditacionEstudio($nk)
    {
        $acreditacion = AcreditacionEstudio::getAcreditacion($nk);

        return $this->render(
            'ver-acreditacion-estudio',
            ['acreditacion' => $acreditacion]
        );
    }

    /**
     * Ver los datos de acreditación institucional de un centro.
     */
    public function actionVerCentroAcreditacion($id)
    {
        $centro = Centro::getCentro($id);

        return $this->render(
            'ver-centro-acreditacion',
            ['centro' => $centro]
        );
    }


    /**
     * Ver los datos de acreditación de un estudio.
     */
    public function actionVerPeriodoEvaluacion($id)
    {
        $estudio = Estudio::getEstudio($id);

        return $this->render(
            'ver-periodo-evaluacion',
            ['estudio' => $estudio]
        );
    }


    /**
     * Ver la dirección de la web específica del plan.
     */
    public function actionVerUrlWebPlan($id)
    {
        $plan = Plan::getPlan($id);

        return $this->render(
            'ver-url-web-plan',
            ['plan' => $plan]
        );
    }


    public function actionVerHorarios()
    {
        $anyo_academico = Calendario::getAnyoAcademico();
        $planes = Plan::find()
            ->innerJoinWith('estudio')
            ->where(['estudio.anyo_academico' => $anyo_academico])
            ->andWhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]])
            ->all();

        return $this->render(
            'ver-horarios',
            ['planes' => $planes, 'anyo' => $anyo_academico, 'siguiente_anyo' => $anyo_academico + 1]
        );
    }

    /**
     * Muestra un listado de los datos de acreditación de los estudios
     */
    public function actionVerAcreditacionEstudios()
    {
        $acreditaciones = AcreditacionEstudio::find()->all();
        return $this->render('ver-acreditacion-estudios', ['acreditaciones' => $acreditaciones]);
    }

    /**
     * Muestra un listado de los periodos de evaluación de los estudios
     */
    public function actionVerPeriodosEvaluacion($anyo)
    {
        $estudios = Estudio::find()
            ->where(['anyo_academico' => $anyo])
            ->andWhere(['activo' => 1])
            ->all();

        return $this->render(
            'ver-periodos-evaluacion',
            [
                'anyo' => intval($anyo),
                'estudios' => $estudios,
            ]
        );
    }

    /**
     * Muestra un listado de los datos de acreditación institucional de los centros
     */
    public function actionVerCentrosAcreditacion()
    {
        $centros = Centro::find()->where(['activo' => 1])->all();

        return $this->render('ver-centros-acreditacion', ['centros' => $centros]);
    }


    /**
     * Muestra un listado de las webs específicas de los planes
     */
    public function actionVerWebsEspecificas($tipo)
    {
        if ($tipo == 'doctorado') {
            $anyo_academico = Calendario::getAnyoDoctorado();
            $planes = Plan::find()
                ->innerJoinWith('estudio')
                ->where(['estudio.anyo_academico' => $anyo_academico])
                ->andWhere(['tipoEstudio_id' => Estudio::DOCT_TIPO_ESTUDIO_ID])
                ->all();
        } else {  // 'grado-master'
            $anyo_academico = Calendario::getAnyoAcademico();
            $planes = Plan::find()
                ->innerJoinWith('estudio')
                ->where(['estudio.anyo_academico' => $anyo_academico])
                ->andWhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]])
                ->all();
        }

        return $this->render(
            'ver-webs-especificas',
            ['anyo' => $anyo_academico, 'siguiente_anyo' => $anyo_academico + 1, 'planes' => $planes, 'tipo' => $tipo]
        );
    }

    public function actionActualizarHorario($id)
    {
        $plan = Plan::getPlan($id);

        return $this->render(
            'actualizar-horario',
            ['plan' => $plan]
        );
    }

    /**
     * Formulario para actualizar los datos de acreditación de un estudio
     */
    public function actionActualizarAcreditacionEstudio($nk)
    {
        $acreditacion = AcreditacionEstudio::getAcreditacion($nk);

        return $this->render(
            'actualizar-acreditacion-estudio',
            ['acreditacion' => $acreditacion]
        );
    }

    /**
     * Formulario para actualizar los datos de acreditación institucional de un centro.
     */
    public function actionActualizarCentroAcreditacion($id)
    {
        $centro = Centro::getCentro($id);

        return $this->render(
            'actualizar-centro-acreditacion',
            ['centro' => $centro]
        );
    }


    /**
     * Formulario para actualizar el periodo de evaluación de un estudio
     */
    public function actionActualizarPeriodoEvaluacion($id)
    {
        $estudio = Estudio::getEstudio($id);

        return $this->render(
            'actualizar-periodo-evaluacion',
            ['estudio' => $estudio]
        );
    }

    /**
     * Formulario para actualizar la dirección de la web específica de un plan.
     */
    public function actionActualizarUrlWebPlan($id)
    {
        $plan = Plan::getPlan($id);

        return $this->render(
            'actualizar-url-web-plan',
            ['plan' => $plan]
        );
    }

    public function actionGuardarHorario()
    {
        $id = Yii::$app->request->post('Plan')['id'];
        $plan = Plan::getPlan($id);

        if ($plan->load(Yii::$app->request->post()) && $plan->update(true, ['url_horarios'])) {
            $nombre = Yii::$app->user->identity->username;
            Yii::info("{$nombre} ha actualizado el horario del plan {$plan->id_nk}.", 'gestion');

            return $this->redirect(
                [
                    'gestion/ver-horario',
                    'id' => $plan->id,
                ]
            );
        } else {
            return $this->render(
                'actualizar-horario',
                ['plan' => $plan]
            );
        }
    }

    /**
     * Carga las URL de los horarios del año anterior.
     */
    public function actionCargarUrlHorariosAnterior()
    {
        $anyo_academico = Calendario::getAnyoAcademico();
        $planes = Plan::find()
            ->innerJoinWith('estudio')
            ->where(['estudio.anyo_academico' => $anyo_academico])
            ->andWhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]])
            ->all();

        foreach ($planes as $plan) {
            if (!isset($plan->url_horarios)) {
                try {
                    $url_ant = Plan::getPlanByNk($anyo_academico-1, $plan->id_nk)->url_horarios;
                } catch (NotFoundHttpException $e) {
                    //$url_ant = "";
                }
                $plan->updateAttributes(['url_horarios'=> $url_ant]);
            }
        }
        return $this->redirect(['gestion/ver-horarios']);
    }

    /**
     * Carga las URL de las webs específicas de los planes del año anterior.
     */
    public function actionCargarWebsEspecificasAnterior($tipo)
    {
        $anyo_academico = Calendario::getAnyoAcademico();
        $tipoEstudio_id = [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID];
        if ($tipo == 'doctorado') {
            $anyo_academico = Calendario::getAnyoDoctorado();
            $tipoEstudio_id = Estudio::DOCT_TIPO_ESTUDIO_ID;
        }
        $planes = Plan::find()
            ->innerJoinWith('estudio')
            ->where(['estudio.anyo_academico' => $anyo_academico])
            ->andWhere(['tipoEstudio_id' => $tipoEstudio_id])
            ->all();

        foreach ($planes as $plan) {
            if (!isset($plan->url_web_plan)) {
                try {
                    $url_ant = Plan::getPlanByNk($anyo_academico-1, $plan->id_nk)->url_web_plan;
                } catch (NotFoundHttpException $e) {
                    //$url_ant = "";
                }
                $plan->updateAttributes(['url_web_plan'=> $url_ant]);
            }
        }
        return $this->redirect(['gestion/ver-webs-especificas', 'tipo' => $tipo]);
    }

    /**
     * Guardar los datos de acreditación de un estudio.
     */
    public function actionGuardarAcreditacionEstudio()
    {
        $nk = Yii::$app->request->post('AcreditacionEstudio')['nk'];
        $acreditacion = AcreditacionEstudio::getAcreditacion($nk);

        if ($acreditacion->load(Yii::$app->request->post())
          and $acreditacion->update(true, ['fecha_verificacion', 'fecha_implantacion', 'fecha_acreditacion', 'anyos_validez'])) {
            $nombre = Yii::$app->user->identity->username;
            Yii::info("{$nombre} ha actualizado los datos de acreditación del estudio {$acreditacion->nk}.", 'gestion');

            return $this->redirect(
                [
                    'gestion/ver-acreditacion-estudio',
                    'nk' => $acreditacion->nk,
                ]
            );
        } else {
            return $this->render(
                'actualizar-acreditacion-estudio',
                ['acreditacion' => $acreditacion]
            );
        }
    }

    /**
     * Guardar los datos de acreditación institucional de un centro.
     */
    public function actionGuardarCentroAcreditacion()
    {
        $id = Yii::$app->request->post('Centro')['id'];
        $centro = Centro::getCentro($id);

        if ($centro->load(Yii::$app->request->post())
          and $centro->update(true, ['acreditacion_url', 'fecha_acreditacion', 'anyos_validez'])) {
            $nombre = Yii::$app->user->identity->username;
            Yii::info("{$nombre} ha actualizado los datos de acreditación del centro {$centro->id}.", 'gestion');

            return $this->redirect(
                [
                    'gestion/ver-centro-acreditacion',
                    'id' => $centro->id,
                ]
            );
        } else {
            return $this->render(
                'actualizar-centro-acreditacion',
                ['centro' => $centro]
            );
        }
    }


    /**
     * Guardar periodo de evaluación de un estudio.
     */
    public function actionGuardarPeriodoEvaluacion()
    {
        $id = Yii::$app->request->post('Estudio')['id'];
        $estudio = Estudio::getEstudio($id);

        if ($estudio->load(Yii::$app->request->post())
          and $estudio->update(true, ['anyos_evaluacion'])) {
            $nombre = Yii::$app->user->identity->username;
            Yii::info("{$nombre} ha actualizado el periodo de evaluación del estudio {$estudio->id}.", 'gestion');

            return $this->redirect(
                [
                    'gestion/ver-periodo-evaluacion',
                    'id' => $estudio->id,
                ]
            );
        } else {
            return $this->render(
                'actualizar-periodo-evaluacion',
                ['estudio' => $estudio]
            );
        }
    }


    /**
     * Guardar el URL de la web específica de un plan.
     */
    public function actionGuardarUrlWebPlan()
    {
        $id = Yii::$app->request->post('Plan')['id'];
        $plan = Plan::getPlan($id);

        if ($plan->load(Yii::$app->request->post()) and $plan->update(true, ['url_web_plan'])) {
            $nombre = Yii::$app->user->identity->username;
            Yii::info("{$nombre} ha actualizado la web específica del plan {$plan->id_nk}.", 'gestion');

            return $this->redirect(
                [
                    'gestion/ver-url-web-plan',
                    'id' => $plan->id,
                ]
            );
        } else {
            return $this->render(
                'actualizar-url-web-plan',
                ['plan' => $plan]
            );
        }
    }

    /**
     * Muestra un listado de los estudios de grado y máster,
     * con enlaces a las estructuras de profesorado de cada estudio y año.
     */
    public function actionVerEstructura($anyo)
    {
        $estudios = Profesorado::getEstudiosdelAnyo($anyo);

        return $this->render(
            'ver-estructura',
            [
                'anyo' => intval($anyo),
                'estudios' => $estudios,
            ]
        );
    }


    /**
     * Muestra un listado de los estudios de grado y máster,
     * con enlaces a los resultados académicos de cada titulación.
     */
    public function actionVerResultadosAcademicos($anyo)
    {
        $estudios = Estudio::find()
            ->where(['anyo_academico' => $anyo])
            ->andWhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]])
            ->andWhere(['activo' => 1])
            ->all();

        return $this->render(
            'ver-resultados-academicos',
            [
                'anyo' => intval($anyo),
                'estudios' => $estudios,
            ]
        );
    }


    /**
     * Muestra un listado de los estudios de grado y máster,
     * con enlaces a la evolución del profesorado de cada estudio.
     */
    public function actionListaEvolucionProfesorado()
    {
        $estudios = Profesorado::getEstudiosdelAnyo(Calendario::getAnyoAcademico() - 1);

        return $this->render('lista-evolucion-profesorado', ['estudios' => $estudios]);
    }

    /**
     * Lista los informes de los estudios del año y tipo indicados.
     */
    public function actionListaInformes($anyo, $tipo)
    {
        Url::remember();
        $language = Yii::$app->language;
        if ('grado-master' == $tipo) {
            $estudios = Estudio::find()
                ->where(['anyo_academico' => $anyo])
                ->andWhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]])
                ->andWhere(['not', ['id_nk' => Estudio::FALSOS_ESTUDIO_IDS]])
                ->andWhere(['activo' => 1])
                ->all();
            $vista = 'lista-informes';
        } elseif ('doctorado' == $tipo) {
            $estudios = Estudio::find()
                ->where(['anyo_academico' => $anyo])
                ->andWhere(['tipoEstudio_id' => Estudio::DOCT_TIPO_ESTUDIO_ID])
                ->andWhere(['activo' => 1])
                ->all();
            $vista = 'lista-informes-doct';
        } elseif ('iced' == $tipo) {
            $estudios = Estudio::find()
                ->where(['tipoEstudio_id' => Estudio::ICED_TIPO_ESTUDIO_ID])
                ->all();
            $vista = 'lista-informes-iced';
        } else {
            throw new NotFoundHttpException(
                sprintf(
                    Yii::t('cati', 'No se ha encontrado el tipo de estudio «%s».  ☹'),
                    $tipo
                )
            );
        }

        $ir = new InformeRespuesta();
        $contestadas = $ir->getContestadas($anyo, $language);

        return $this->render(
            $vista,
            [
                'anyo' => intval($anyo),
                'estudios' => $estudios,
                'contestadas' => $contestadas,
            ]
        );
    }

    public function actionListaInformesVersion($anyo, $tipo, $version)
    {
        Url::remember();
        $language = Yii::$app->language;
        if ('grado-master' == $tipo) {
            $condicion = ['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]];
        } elseif ('doctorado' == $tipo) {
            $condicion = ['tipoEstudio_id' => Estudio::DOCT_TIPO_ESTUDIO_ID];
        } else {
            throw new NotFoundHttpException(
                sprintf(
                    Yii::t('cati', 'No se ha encontrado el tipo de estudio «%s».  ☹'),
                    $tipo
                )
            );
        }

        $estudios = Estudio::find()->where(['anyo_academico' => $anyo])->andWhere($condicion)->andWhere(['activo' => 1])->all();
        $estudios = array_filter(
            $estudios,
            function ($estudio) use ($anyo, $version) {
                return $estudio->getVersionInforme($anyo) === intval($version);
            }
        );

        $ip = new InformePublicado();
        $informes_publicados = $ip->getPublicados($anyo, $language);
        $ir = new InformeRespuesta();
        $contestadas = $ir->getContestadas($anyo, $language);

        return $this->render(
            'lista-informes-version',
            [
                'anyo' => intval($anyo),
                'contestadas' => $contestadas,
                'estudios' => $estudios,
                'informes_publicados' => $informes_publicados,
                'tipo' => $tipo,
                'version' => intval($version),
            ]
        );
    }

    /**
     * Lista los planes anuales de innovación y mejora
     * de los estudios de grado y máster del año indicado.
     */
    public function actionListaPlanes($anyo, $tipo)
    {
        Url::remember();
        $language = Yii::$app->language;
        $e = new Estudio();
        $datos = $e->getListadoPlanes($anyo, $language, $tipo);

        return $this->render(
            'lista-planes',
            [
                'anyo' => intval($anyo),
                'datos' => $datos,
                'tipo' => $tipo,
            ]
        );
    }

    /**
     * Muestra enlaces a las páginas para editar las informaciones de cada uno
     * de los estudios del tipo indicado.
     */
    public function actionListaInformaciones($tipo)
    {
        $anyo_academico = Calendario::getAnyoAcademico();
        if ('grado-master' == $tipo) {
            $estudios = Estudio::find()
                ->where(['anyo_academico' => $anyo_academico])
                ->andWhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]])
                ->andWhere(['activo' => 1])
                ->all();
        } else {  // Doctorado
            $estudios = Estudio::find()
                ->where(['anyo_academico' => $anyo_academico])
                ->andWhere(['tipoEstudio_id' => Estudio::DOCT_TIPO_ESTUDIO_ID])
                ->andWhere(['activo' => 1])
                ->all();
        }

        return $this->render(
            'lista-informaciones',
            [
                'estudios' => $estudios,
                'tipo' => $tipo,
            ]
        );
    }

    /**
     * Muestra enlaces a las páginas para editar las notas de cada plan.
     */
    public function actionListaNotasPlanes()
    {
        $planes = Plan::find()
            ->innerJoinWith('estudio')
            ->where(['plan.anyo_academico' => Calendario::getAnyoAcademico()])
            ->andWhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]])
            ->andWhere(['plan.activo' => 1])
            ->all();

        return $this->render(
            'lista-notas-planes',
            ['planes' => $planes]
        );
    }

    /**
     * Vuelve a abrir la última versión publicada del informe de esa titulación y año.
     */
    public function actionAbrirInforme($estudio_id, $anyo)
    {
        $language = Yii::$app->request->cookies->getValue('language', 'es');
        $estudio = Estudio::getEstudio($estudio_id);
        $ip = InformePublicado::findOne(['estudio_id' => $estudio_id, 'anyo' => $anyo, 'language' => $language]);
        if (!$ip) {
            $ip = new InformePublicado();
            $ip->estudio_id = $estudio_id;
            $ip->anyo = $anyo;
            $ip->language = $language;
            $ip->estudio_id_nk = $estudio->id_nk;
        }
        if ($ip->version > 0) {
            $ip->version = $ip->version - 1;
            if ($ip->save()) {
                $nombre = Yii::$app->user->identity->username;
                Yii::info(
                    sprintf('%s ha abierto la versión %d del informe del estudio %d.', $nombre, $ip->version + 1, $estudio_id),
                    'gestion'
                );
            }
        }

        return $this->redirect(Url::previous());
    }

    /**
     * Vuelve a abrir la última versión publicada del plan de innovación
     * y mejora de esa titulación y año.
     */
    public function actionAbrirPlan($estudio_id, $anyo)
    {
        $language = Yii::$app->request->cookies->getValue('language', 'es');
        $estudio = Estudio::getEstudio($estudio_id);
        $pp = PlanPublicado::findOne(['estudio_id' => $estudio_id, 'anyo' => $anyo, 'language' => $language]);
        if (!$pp) {
            $pp = new PlanPublicado();
            $pp->estudio_id = $estudio_id;
            $pp->anyo = $anyo;
            $pp->language = $language;
            $pp->estudio_id_nk = $estudio->estudio_id_nk;
        }
        if ($pp->version > 0) {
            $pp->version = $pp->version - 1;
            if ($pp->save()) {
                $nombre = Yii::$app->user->identity->username;
                Yii::info(
                    sprintf('%s ha abierto la versión %d del PAIM del estudio %d', $nombre, $pp->version + 1, $estudio_id),
                    'gestion'
                );
            }
        }

        return $this->redirect(Url::previous());
    }

    /**
     * Cierra la versión actualmente en edición del informe de esa titulación y año.
     */
    public function actionCerrarInforme($estudio_id, $anyo)
    {
        $language = Yii::$app->request->cookies->getValue('language', 'es');
        $estudio = Estudio::getEstudio($estudio_id);
        $ip = InformePublicado::findOne(['estudio_id' => $estudio_id, 'anyo' => $anyo, 'language' => $language]);
        if (!$ip) {
            $ip = new InformePublicado([
                'estudio_id' => $estudio_id,
                'anyo' => $anyo,
                'language' => $language,
                'estudio_id_nk' => $estudio->id_nk,
            ]);
        }
        if ($ip->version < $ip->getVersionMaxima()) {
            $ip->version = $ip->version + 1;
            if ($ip->save()) {
                $nombre = Yii::$app->user->identity->username;
                Yii::info(
                    "$nombre ha cerrado la versión {$ip->version} del informe del estudio $estudio_id",
                    'gestion'
                );
            }
        }

        return $this->redirect(Url::previous());
    }

    /**
     * Cierra la versión actualmente en edición del plan de innovación y mejora
     * de esa titulación y año.
     */
    public function actionCerrarPlan($estudio_id, $anyo)
    {
        $language = Yii::$app->request->cookies->getValue('language', 'es');
        $estudio = Estudio::getEstudio($estudio_id);
        $pp = PlanPublicado::findOne(['estudio_id' => $estudio_id, 'anyo' => $anyo, 'language' => $language]);
        if (!$pp) {
            $pp = new PlanPublicado([
                'estudio_id' => $estudio_id,
                'anyo' => $anyo,
                'language' => $language,
                'estudio_id_nk' => $estudio->id_nk,
            ]);
        }
        if ($pp->version < $pp->getVersionMaxima()) {
            $pp->version = $pp->version + 1;
            if ($pp->save()) {
                $nombre = Yii::$app->user->identity->username;
                Yii::info("{$nombre} ha cerrado la versión {$pp->version} del PAIM del estudio {$estudio_id}.", 'gestion');
            }
        }

        return $this->redirect(Url::previous());
    }

    /**
     * Permite al usuario seleccionar una pregunta del informe.
     * Esta pregunta se usará para generar los extractos.
     */
    public function actionSeleccionarPregunta($anyo, $tipo)
    {
        // El campo apartado es una cadena, por lo que se ordena alfabéticamente
        // y 10 va después de 1 en lugar de después de 9.
        // Con esta expresión convertimos a tipo numérico los primeros 3 caracteres
        // y ordenamos correctamente.  En MS SQLServer usar SUBSTRING().
        $exp = new Expression('CAST(SUBSTR(apartado, 1, 3) AS DECIMAL), apartado');
        $preguntas = InformePregunta::find()
            ->where(['anyo' => $anyo, 'editable' => 1, 'tipo' => $tipo])
            ->orderBy($exp)
            ->all();

        return $this->render(
            'seleccionar-pregunta',
            [
                'anyo' => intval($anyo),
                'preguntas' => $preguntas,
                'tipo' => $tipo,
            ]
        );
    }

    /**
     * Permite al usuario seleccionar una pregunta del plan de mejora.
     * Esta pregunta se usará para generar los extractos.
     */
    public function actionSeleccionarPreguntaPlan($anyo, $tipo)
    {
        // El campo apartado es una cadena, por lo que se ordena alfabéticamente
        // y 10 va después de 1 en lugar de después de 9.
        // Con esta expresión convertimos a tipo numérico los primeros 3 caracteres
        // y ordenamos correctamente.  En MS SQLServer usar SUBSTRING().
        $exp = new Expression('CAST(SUBSTR(apartado, 1, 3) AS DECIMAL), apartado');
        $preguntas = PlanPregunta::find()
            ->where(['anyo' => $anyo, 'tipo' => $tipo])
            ->orderBy($exp)
            ->all();

        return $this->render(
            'seleccionar-pregunta-plan',
            [
                'anyo' => intval($anyo),
                'preguntas' => $preguntas,
                'tipo' => $tipo,
            ]
        );
    }

    /**
     * Permite al usuario seleccionar un centro.
     * Este centro se usará para generar los extractos de los PAIM.
     */
    public function actionSeleccionarCentroPaim($anyo)
    {
        $centros = Centro::find()->where(['activo' => 1])->innerJoinWith('translations')->orderBy('nombre')->all();

        return $this->render('seleccionar-centro-paim', ['anyo' => intval($anyo), 'centros' => $centros]);
    }

    /**
     * Muestra las contestaciones de todas las titulaciones a una de las
     * preguntas del informe.
     */
    public function actionExtractos($anyo, $pregunta_id, $tipo)
    {
        $pregunta = InformePregunta::findOne(['id' => $pregunta_id]);
        $respuestas = InformeRespuesta::find()
            ->where(['anyo' => $anyo, 'informe_pregunta_id' => $pregunta_id])
            ->orderBy('estudio_id')
            ->all();

        return $this->render(
            'extractos',
            [
                'anyo' => intval($anyo),
                'pregunta' => $pregunta,
                'respuestas' => $respuestas,
                'tipo' => $tipo,
            ]
        );
    }

    /**
     * Muestra las contestaciones de todas las titulaciones a una de las
     * preguntas del plan de innovación y mejora.
     */
    public function actionExtractosPlan($anyo, $pregunta_id, $tipo)
    {
        $pregunta = PlanPregunta::findOne(['id' => $pregunta_id]);
        $respuestas = PlanRespuesta::find()
            ->where(['anyo' => $anyo, 'plan_pregunta_id' => $pregunta_id])
            ->orderBy('estudio_id')
            ->all();

        return $this->render(
            'extractos-plan',
            [
                'anyo' => intval($anyo),
                'pregunta' => $pregunta,
                'respuestas' => $respuestas,
                'tipo' => $tipo,
            ]
        );
    }

    /**
     * Muestra las contestaciones de todas las titulaciones de un centro
     * a las preguntas del plan anual de innovación y mejora.
     */
    public function actionExtractosPaimCentro($anyo, $centro_id)
    {
        $centro = Centro::getCentro($centro_id);
        $exp = new Expression('CAST(SUBSTR(apartado, 1, 3) AS DECIMAL), apartado');
        $preguntas = PlanPregunta::find()->where(['anyo' => $anyo])->orderBy($exp)->all();
        $respuestas = PlanRespuesta::find()
            ->where(['anyo' => $anyo])
            ->innerJoinWith('estudio.plans.centro')
            ->where(['centro.id' => $centro_id])
            ->orderBy(['plan_pregunta_id' => SORT_ASC, 'estudio_id' => SORT_ASC])
            ->all();

        return $this->render(
            'extractos-paim-centro',
            [
                'anyo' => intval($anyo),
                'centro' => $centro,
                'preguntas' => $preguntas,
                'respuestas' => $respuestas,
                'tipo' => null,
            ]
        );
    }

    /*
     * Muestra las guias docentes publicadas.
     *
    public function actionGuiasDocentes()
    {
        $anyo_academico = date('m') < 6 ? date('Y') - 1 : date('Y');
        $aecp = new Aecp();
        $guias = $aecp->getGuiasPublicadas($anyo_academico);

        return $this->render('guias-publicadas', [
            'guias' => $guias,
            'anyo_academico' => $anyo_academico,
        ]);
    }
    */

    /**
     * Muestra un listado de los programas de doctorado para subir sus indicadores.
     */
    public function actionListaIndicadores()
    {
        $planes = Plan::find()
            ->innerJoinWith('estudio')
            ->where(['estudio.anyo_academico' => Calendario::getAnyoAcademico()])
            ->andWhere(['tipoEstudio_id' => Estudio::DOCT_TIPO_ESTUDIO_ID])
            ->all();
        $dpPlanes = new ArrayDataProvider(
            [
                'allModels' => $planes,
                'pagination' => false,  // ['pageSize' => 10],
                'sort' => [
                    'attributes' => ['estudio_id_nk', 'id_nk', 'estudio.nombre'],
                    'defaultOrder' => ['estudio.nombre' => SORT_ASC],
                ],
            ]
        );

        return $this->render(
            'lista-indicadores',
            ['dpPlanes' => $dpPlanes]
        );
    }

    /**
     * Muestra formulario para subir PDFs de los indicadores de doctorado 2013-2016.
     */
    public function actionSubirIndicadores($estudio_id_nk)
    {
        $estudio = Estudio::getEstudioByNk(Calendario::getAnyoAcademico(), $estudio_id_nk);
        $model = new UploadPdf();

        if (Yii::$app->request->isPost) {
            $model->pdfFile = UploadedFile::getInstance($model, 'pdfFile');
            if ($model->upload('indicadores', "indicadores-{$estudio_id_nk}.pdf")) {
                // El fichero se ha guardado con éxito
                Yii::$app->session->addFlash(
                    'success',
                    sprintf(Yii::t('cati', 'Indicadores de %s guardados con éxito.'), $estudio->nombre)
                );

                return $this->redirect(['lista-indicadores']);
            } else {
                // Error al subir el fichero
                Yii::$app->session->addFlash(
                    'danger',
                    sprintf(
                        Yii::t('cati', 'Al subir el fichero se produjo el siguiente error:') . '<br>%s',
                        $model->getErrorMessage()
                    )
                );
            }
        }

        return $this->render('subir-indicadores', ['model' => $model, 'estudio' => $estudio]);
    }

    /**
     * Muestra formulario para subir PDFs de los procedimientos del Sistema Interno de Gestión de la Calidad
     */
    public function actionSubirProcedimiento()
    {
        $model = new UploadPdf();

        if (Yii::$app->request->isPost) {
            $model->pdfFile = UploadedFile::getInstance($model, 'pdfFile');

            if (isset($model->pdfFile)) {
                $cleanName = $model->getSlug();
                $without_extension = substr($cleanName, 0, strrpos($cleanName, '.'));
                $cleanName = strtoupper($without_extension) . '.pdf';
                try {
                    if ($model->upload('procedimientos', $cleanName)) {
                        // El fichero se ha guardado con éxito
                        Yii::$app->session->addFlash(
                            'success',
                            sprintf(Yii::t('cati', 'Procedimiento guardado con el nombre «<strong>%s</strong>».'), $cleanName) . '<br>'
                              . Yii::t('cati', 'Recuerde actualizar los enlaces en la página de procedimientos si es necesario.')
                        );
                    } else {
                        // Error al subir el fichero
                        Yii::$app->session->addFlash(
                            'danger',
                            sprintf(
                                Yii::t('cati', 'Al subir el fichero «%s» se produjo el siguiente error:') . '<br>%s',
                                $cleanName,
                                $model->getErrorMessage()
                            )
                        );
                    }
                } catch (\Exception $e) {
                    // Fichero subido al servidor, pero move_uploaded_file() produjo un warning al intentar ponerlo en su sitio
                    $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
                    $model->addError('_exception', $msg);
                    Yii::$app->session->addFlash('danger', "ERROR:<br>{$msg}");
                }
            }

            return $this->redirect(['subir-procedimiento']);
        }

        return $this->render('subir-procedimiento', ['model' => $model]);
    }

    /**
     * Muestra página con botones para cargar los informes y planes de mejora a Zaguán.
     */
    public function actionCargarAZaguan($anyo, $tipo)
    {
        if ($tipo == 'grado-master') {
            return $this->render('cargar-a-zaguan', ['anyo' => $anyo]);
        } elseif ($tipo == 'doctorado' or $tipo == 'iced') {
            return $this->render('cargar-a-zaguan-doct', ['anyo' => $anyo]);
        }
        throw new NotFoundHttpException(
            sprintf(
                Yii::t('cati', 'No se ha encontrado el tipo de estudio «%s».  ☹'),
                $tipo
            )
        );
    }

    /**
     * Ejecuta en segundo plano el script `datuz/datuz2cati.php`.
     */
    public function actionActualizarDatosAcademicos()
    {
        if (Yii::$app->request->isPost) {
            $curso = intval(Yii::$app->request->post('curso'));
            $cmd = Yii::getAlias('@app') . "/../scripts/datuz/datuz2cati.php --curso={$curso}";
            $bgprocess = new BackgroundProcess($cmd);
            $bgprocess->run();

            Yii::$app->session->addFlash(
                'success',
                Yii::t('gestion', 'Se ha lanzado la actualización de los datos académicos.  En breve deberían estar disponibles.')
            );

            return $this->redirect(['calidad']);
        }

        return $this->render('actualizar-datos-academicos');
    }

    /**
     * Ejecuta en segundo plano los scripts `datuz/doct2cati.php` y `datuz/doctrama2cati.php`.
     */
    public function actionActualizarDatosDoctorado()
    {
        if (Yii::$app->request->isPost) {
            $curso = intval(Yii::$app->request->post('curso'));
            $cmd = Yii::getAlias('@app') . "/../scripts/datuz/doct2cati.php --curso={$curso}";
            $bgprocess = new BackgroundProcess($cmd);
            $bgprocess->run();
            $cmd2 = Yii::getAlias('@app') . "/../scripts/datuz/doctrama2cati.php --curso={$curso}";
            $bgprocess2 = new BackgroundProcess($cmd2);
            $bgprocess2->run();

            Yii::$app->session->addFlash(
                'success',
                Yii::t('gestion', 'Se ha lanzado la actualización de los datos de doctorado.  En breve deberían estar disponibles.')
            );

            return $this->redirect(['index']);
        }

        return $this->render('actualizar-datos-doctorado');
    }

    /**
     * Ejecuta en segundo plano el script atenea/atenea2cati.sh
     *
     * El proceso web debe tener permisos sobre el directorio de salida.
     * El usuario también, para las actualizaciones programadas vía cron.
     */
    public function actionActualizarEncuestas()
    {
        $encuestas = [
            ['id' => 0, 'clave' => 'ensenanza', 'desc' => Yii::t('cati', 'Evaluación de la enseñanza')],
            ['id' => 1, 'clave' => 'practicas', 'desc' => Yii::t('cati', 'Evaluación de las prácticas externas por los alumnos')],
            ['id' => 2, 'clave' => 'satisfaccionPAS', 'desc' => Yii::t('cati', 'Satisfacción del PAS con el centro')],
            ['id' => 3, 'clave' => 'satisfaccionPDI', 'desc' => Yii::t('cati', 'Satisfacción del PDI con la titulación')],
            ['id' => 4, 'clave' => 'movilidad', 'desc' => Yii::t('cati', 'Programas de movilidad: Erasmus')],
            ['id' => 5, 'clave' => 'satisfaccionTitulacion', 'desc' => Yii::t('cati', 'Satisfacción de los estudiantes con la titulación')],
            ['id' => 6, 'clave' => 'TfgTfm', 'desc' => Yii::t('cati', 'Satisfacción con el Trabajo de Fin de Grado o Máster')],
            ['id' => 7, 'clave' => 'doctorado', 'desc' => Yii::t('cati', 'Satisfacción con el doctorado')],
            ['id' => 8, 'clave' => 'doctoradoEgresados', 'desc' => Yii::t('cati', 'Satisfacción e inserción laboral de egresados de la Escuela de Doctorado')],
            ['id' => 9, 'clave' => 'clinicas', 'desc' => Yii::t('cati', 'Evaluación de las prácticas clínicas por los alumnos')],
        ];
        $claves = yii\helpers\ArrayHelper::map($encuestas, 'id', 'clave');
        $descripciones = yii\helpers\ArrayHelper::map($encuestas, 'id', 'desc');

        if (Yii::$app->request->isPost) {
            $anyo = intval(Yii::$app->request->post('curso'));
            $indice = intval(Yii::$app->request->post('indice'));
            $encuesta = yii\helpers\ArrayHelper::getValue($claves, $indice, '');
            $cmd = Yii::getAlias('@app') . "/../scripts/atenea/atenea2cati.sh {$anyo} {$encuesta}";
            $bgprocess = new BackgroundProcess($cmd);
            $bgprocess->run();

            Yii::$app->session->addFlash(
                'success',
                sprintf(
                    Yii::t('gestion', 'Se ha lanzado la actualización de las encuestas de «%s».  En breve deberían estar actualizadas.'),
                    $descripciones[$indice]
                )
            );

            return $this->redirect(['calidad']);
        }

        return $this->render('actualizar-encuestas', ['descripciones' => $descripciones]);
    }
}
