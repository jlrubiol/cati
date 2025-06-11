<?php
/**
 * /home/quique/Devel/cati/cati/runtime/giiant/49eb2de82346bc30092f584268252ed2.
 */

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use app\models\Agente;
use app\models\Calendario;
use app\models\Estudio;
use app\models\Plan;

/**
 * This is the class for controller "AgenteController".
 */
class AgenteController extends \app\controllers\base\AgenteController
{
    public function behaviors()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $plan_id = $request->get('plan_id');

        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['lista'],
                        'allow' => true,
                        // 'roles' option not set => this rule applies to all roles
                    ], [
                        'actions' => ['crear-delegado', 'borrar-delegado', 'editar-delegado', 'lista-delegados-plan'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) use ($plan_id) {
                            $usuario = Yii::$app->user;
                            $plan = Plan::getPlan($plan_id);
                            if (!$plan) {
                                return false;
                            }
                            $estudio = $plan->estudio;

                            return Yii::$app->user->can('editarInforme', ['estudio' => $estudio]);
                        },
                        'roles' => ['@'],
                    ], [
                        'actions' => ['lista-delegados', 'borrar-delegado-cgc', 'crear-delegado-cgc', 'editar-delegado-cgc', 'lista-delegados-cgc', 'lista-delegados-cgc-plan', 'ver-delegados', 'ver-delegados-cgc'],
                        'allow' => true,
                        'roles' => ['unidadCalidad', 'gradoMaster'],
                    ], [
                        'actions' => ['lista-delegados-doct'],
                        'allow' => true,
                        'roles' => ['unidadCalidad', 'escuelaDoctorado'],
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


    /**
     * Lista los agentes de la titulación indicada.
     *
     * @return mixed
     */
    public function actionLista($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $datos = Agente::getAgentesDelEstudio($estudio_id);

        return $this->render('lista', [
            'estudio' => $estudio,
            'datos' => $datos,
        ]);
    }

    /** Borra un delegado del coordinador de un plan de estudios */
    public function actionBorrarDelegado($id, $plan_id)
    {
        try {
            $model = $this->findModel($id);

            // $plan_id se usa para comprobar que el usuario tiene permisos para acceder a este método.
            $plan = Plan::getPlan($plan_id);
            if ($model->plan_id_nk != $plan->id_nk) {
                throw new ServerErrorHttpException(Yii::t(
                    'cati',
                    'Datos inconsistentes.  El plan del registro no coincide con el de la petición.'
                ));
            }

            $model->delete();
            Yii::$app->session->addFlash(
                'success',
                sprintf(Yii::t('gestion', 'Se ha borrado un delegado del coordinador del plan %d.'), $model->plan_id_nk)
            );
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            Yii::$app->getSession()->addFlash('error', $msg);

            return $this->redirect(Url::previous());
        }

        return $this->redirect(['lista-delegados-plan', 'plan_id' => $plan_id]);
    }

    /** Borra un delegado del presidente de la CGC de un plan de estudios */
    public function actionBorrarDelegadoCgc($id, $plan_id)
    {
        try {
            $model = $this->findModel($id);

            $model->delete();
            Yii::$app->session->addFlash(
                'success',
                sprintf(Yii::t('gestion', 'Se ha borrado un delegado del presidente de la CGC del plan %d.'), $model->plan_id_nk)
            );
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            Yii::$app->getSession()->addFlash('error', $msg);

            return $this->redirect(Url::previous());
        }

        return $this->redirect(['lista-delegados-cgc-plan', 'plan_id' => $plan_id]);
    }

    /** Crea un nuevo delegado del coordinador de un plan de estudios */
    public function actionCrearDelegado($plan_id)
    {
        $plan = Plan::getPlan($plan_id);
        $model = new Agente();

        try {
            if ($model->load(Yii::$app->request->post())) {
                $model->centro_id = $plan->centro_id;
                $model->plan_id_nk = $plan->id_nk;
                $model->estudio_id = $plan->estudio_id_nk;
                $model->estudio_id_nk = $plan->estudio_id_nk;
                $model->comision_id = 'delegado';
                $model->rol = 'Delegado';
                if ($model->save()) {
                    $nombre = Yii::$app->user->identity->username;
                    Yii::info("{$nombre} ha creado un nuevo delegado del coordinador del plan {$plan_id}.", 'coordinadores');

                    return $this->redirect(['lista-delegados-plan', 'plan_id' => $plan_id]);
                }
            } elseif (!\Yii::$app->request->isPost) {
                $model->load(Yii::$app->request->get());
                // $model->attributes = Yii::$app->request->get();
            }
        } catch (Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
            Yii::$app->getSession()->addFlash('error', $msg);
        }

        return $this->render('crear-delegado', [
            'model' => $model,
            'plan' => $plan,
        ]);
    }

    /** Crea un nuevo delegado del presidente de la CGC de un plan de estudios */
    public function actionCrearDelegadoCgc($plan_id)
    {
        $plan = Plan::getPlan($plan_id);
        $model = new Agente();

        try {
            if ($model->load(Yii::$app->request->post())) {
                $model->centro_id = $plan->centro_id;
                $model->plan_id_nk = $plan->id_nk;
                $model->estudio_id = $plan->estudio_id_nk;
                $model->estudio_id_nk = $plan->estudio_id_nk;
                $model->comision_id = 'dele_cgc';
                $model->rol = 'Delegado presidente CGC';
                if ($model->save()) {
                    $nombre = Yii::$app->user->identity->username;
                    Yii::info("{$nombre} ha creado un nuevo delegado del presidente de la CGC del plan {$plan_id}.", 'coordinadores');

                    return $this->redirect(['lista-delegados-cgc-plan', 'plan_id' => $plan_id]);
                }
            } elseif (!\Yii::$app->request->isPost) {
                $model->load(Yii::$app->request->get());
                // $model->attributes = Yii::$app->request->get();
            }
        } catch (Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
            Yii::$app->getSession()->addFlash('error', $msg);
        }

        return $this->render('crear-delegado-cgc', [
            'model' => $model,
            'plan' => $plan,
        ]);
    }

    /** Actualiza un delegado del coordinador de un plan de estudios. */
    public function actionEditarDelegado($id, $plan_id)
    {
        $model = $this->findModel($id);
        // $plan_id se usa para comprobar que el usuario tiene permisos para acceder a este método.
        $plan = Plan::getPlan($plan_id);
        if ($model->plan_id_nk != $plan->id_nk) {
            throw new ServerErrorHttpException(Yii::t(
                'cati',
                'Datos inconsistentes.  El plan del registro no coincide con el de la petición.'
            ));
        }

        if ($model->load(Yii::$app->request->post())
            && $model->update(true, ['nombre', 'apellido1', 'apellido2', 'nip', 'email'])) {
            $nombre = Yii::$app->user->identity->username;
            Yii::info("{$nombre} ha actualizado un delegado del coordinador del plan {$plan_id}.", 'coordinadores');
            Yii::$app->session->addFlash(
                'success',
                sprintf(Yii::t('gestion', 'Se ha actualizado el delegado del coordinador del plan %d.'), $model->plan_id_nk)
            );

            return $this->redirect(Url::to([
                'lista-delegados-plan',
                'plan_id' => $plan_id,
            ]));
        } else {
            foreach ($model->getErrors() as $campo_con_errores) {
                foreach ($campo_con_errores as $error) {
                    Yii::$app->session->addFlash('error', $error);
                }
            }

            $plan = Plan::getPlan($plan_id);
            return $this->render('editar-delegado', ['model' => $model, 'plan' => $plan]);
        }
    }

    /** Actualiza un delegado del presidente de la CGC de un plan de estudios. */
    public function actionEditarDelegadoCgc($id, $plan_id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())
            && $model->update(true, ['nombre', 'apellido1', 'apellido2', 'nip', 'email'])) {
            $nombre = Yii::$app->user->identity->username;
            Yii::info("{$nombre} ha actualizado un delegado del presidente de la CGC del plan {$plan_id}.", 'coordinadores');
            Yii::$app->session->addFlash(
                'success',
                sprintf(Yii::t('gestion', 'Se ha actualizado el delegado del presidente de la CGC del plan %d.'), $model->plan_id_nk)
            );

            return $this->redirect(Url::to([
                'lista-delegados-cgc-plan',
                'plan_id' => $plan_id,
            ]));
        } else {
            foreach ($model->getErrors() as $campo_con_errores) {
                foreach ($campo_con_errores as $error) {
                    Yii::$app->session->addFlash('error', $error);
                }
            }

            $plan = Plan::getPlan($plan_id);
            return $this->render('editar-delegado-cgc', ['model' => $model, 'plan' => $plan]);
        }
    }

    /**
     * Muestra un listado de los planes de grado y máster,
     * con un enlace a la página de los delegados del coordinador de cada plan.
     */
    public function actionListaDelegados()
    {
        $anyo_academico = Calendario::getAnyoAcademico();
        $planes = Plan::find()
            ->innerJoinWith('estudio')
            ->where(['estudio.anyo_academico' => $anyo_academico])
            ->andWhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]])
            ->all()
        ;

        return $this->render('//agente/lista-delegados', [
            'planes' => $planes,
        ]);
    }

    /**
     * Muestra un listado de los delegados de los estudios de grado, máster y doctorado,
     * con su dirección de correo.
     */
    public function actionVerDelegados($anyo_academico = null)
    {
        if (!$anyo_academico) {
            $anyo_academico = Calendario::getAnyoAcademico();
        }
        $delegados = Agente::find()
            ->where(['comision_id' => 'delegado'])
            ->andWhere(['!=', 'estudio_id_nk', 99999])  # ICED
            ->orderBy('estudio_id_nk', 'plan_id_nk')
            ->all();

        return $this->render(
            'ver-delegados',
            ['anyo_academico' => $anyo_academico, 'delegados' => $delegados]
        );
    }

    /**
     * Muestra un listado de los planes de grado y máster,
     * con un enlace a la página de los delegados de la Comisión de Garantía de la Calidad de cada plan.
     */
    public function actionListaDelegadosCgc()
    {
        $anyo_academico = Calendario::getAnyoAcademico();
        $planes = Plan::find()
            ->innerJoinWith('estudio')
            ->where(['estudio.anyo_academico' => $anyo_academico])
            ->andWhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]])
            ->all()
        ;

        return $this->render('//agente/lista-delegados-cgc', [
            'planes' => $planes,
        ]);
    }

    /**
     * Muestra un listado de los delegados de las Comisiones de Garantía de la Calidad
     * de los planos de grado y máster, con su dirección de correo.
     */
    public function actionVerDelegadosCgc()
    {
        $delegados = Agente::find()
            ->where(['comision_id' => 'dele_cgc'])
            ->orderBy('estudio_id_nk', 'plan_id_nk')
            ->all();

        return $this->render(
            'ver-delegados-cgc',
            ['delegados' => $delegados]
        );
    }

    /**
     * Muestra un listado de los planes de doctorado,
     * con un enlace a la página de delegados de cada plan.
     */
    public function actionListaDelegadosDoct()
    {
        $anyo_academico = Calendario::getAnyoDoctorado();
        $planes = Plan::find()
            ->innerJoinWith('estudio')
            ->where(['estudio.anyo_academico' => $anyo_academico])
            ->andWhere(['tipoEstudio_id' => Estudio::DOCT_TIPO_ESTUDIO_ID])
            ->all()
        ;

        return $this->render('//agente/lista-delegados', [
            'planes' => $planes,
        ]);
    }

    /** Muestra los delegados de los coordinadores de un plan de estudios */
    public function actionListaDelegadosPlan($plan_id)
    {
        Url::remember();
        $plan = Plan::getPlan($plan_id);
        $delegados = Agente::find()
            ->where(['plan_id_nk' => $plan->id_nk, 'comision_id' => 'delegado'])
            ->all();

        return $this->render('lista-delegados-plan', [
            'delegados' => $delegados,
            'plan' => $plan,
        ]);
    }

    /** Muestra los delegados de los presidentes de la CGC de un plan de estudios */
    public function actionListaDelegadosCgcPlan($plan_id)
    {
        Url::remember();
        $plan = Plan::getPlan($plan_id);
        $delegados_cgc = Agente::find()
            ->where(['plan_id_nk' => $plan->id_nk, 'comision_id' => 'dele_cgc'])
            ->all();
        return $this->render('lista-delegados-cgc-plan', [
            'delegados_cgc' => $delegados_cgc,
            'plan' => $plan,
        ]);
    }
}
