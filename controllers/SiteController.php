<?php

namespace app\controllers;

use Yii;
use app\models\Calendario;
use app\models\Estudio;
use app\models\InformePublicado;
use app\models\Plan;
use app\models\PlanPublicado;
use app\controllers\base\CatiController;

class SiteController extends CatiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'rules' => [
                    [
                        // 'actions' => ['index', 'acpua', 'acpua-doct', 'acpua-historico', 'ayuda', 'error', 'captcha'],
                        'allow' => true,
                    ],
                ],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $estudios = Estudio::getEstudiosActivos();

        return $this->render('index', [
            'estudios' => $estudios,
        ]);
    }


    /**
     * Displays help page.
     *
     * @return string
     */
    public function actionAyuda()
    {
        return $this->render('ayuda');
    }


    /**
     * Displays page with links to PDFs of reports and plans for each degree.
     *
     * @return string
     */
    public function actionAcpua($anyo = null)
    {
        $ultimo_anyo = InformePublicado::find()->select('anyo')->orderBy(['anyo' => SORT_DESC])->limit(1)->column()[0];
        if (!$anyo) {
            $anyo = $ultimo_anyo;
        }
        $language = Yii::$app->language;
        $estudios = Estudio::find()
            ->where(['anyo_academico' => $anyo])
            ->andwhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]])
            ->andWhere(['not', ['id_nk' => Estudio::FALSOS_ESTUDIO_IDS]])
            //  ->andWhere(['activo' => 1])
            ->all();

        $informes_publicados = InformePublicado::getPublicados($anyo, $language);
        $planes_publicados = PlanPublicado::getPublicados($anyo, $language);

        return $this->render('acpua', [
            'anyo' => intval($anyo),
            'estudios' => $estudios,
            'informes_publicados' => $informes_publicados,
            'planes_publicados' => $planes_publicados,
            'siguiente_anyo' => intval($anyo) + 1,
            'ultimo_anyo' => $ultimo_anyo,
        ]);
    }

    /**
     * Legacy.  Sólo para evitar romper enlaces.
     * Displays page with links to PDFs of old reports and plans for each degree.
     *
     * @return string
     */
    public function actionAcpuaHistorico($anyo)
    {
        return $this->redirect(['acpua', 'anyo' => $anyo]);
    }

    /**
     * Muestra una página con enlaces a los informes y planes de cada programa de doctorado.
     *
     * @return string
     */
    public function actionAcpuaDoct($anyo = null)
    {
        $ultimo_anyo = InformePublicado::find()->select('anyo')->orderBy(['anyo' => SORT_DESC])->limit(1)->column()[0];
        if (!$anyo) {
            $anyo = $ultimo_anyo;
        }
        $language = Yii::$app->language;
        $estudios = Estudio::find()
            ->where(['anyo_academico' => $anyo])
            ->andWhere(['tipoEstudio_id' => Estudio::DOCT_TIPO_ESTUDIO_ID])
            ->andWhere(['activo' => 1])
            ->all();

        $informes_publicados = InformePublicado::getPublicados($anyo, $language);
        $planes_publicados = PlanPublicado::getPublicados($anyo, $language);

        return $this->render('acpua-doct', [
            'anyo' => intval($anyo),
            'estudios' => $estudios,
            'informes_publicados' => $informes_publicados,
            'planes_publicados' => $planes_publicados,
            'siguiente_anyo' => intval($anyo) + 1,
            'ultimo_anyo' => $ultimo_anyo,
        ]);
    }

    /**
     * Muestra un listado de los planes de grado y máster,
     * con enlaces a los PDF de las encuestas de cada plan.
     */
    public function actionVerEncuestas($anyo=null)
    {
        $anyo_academico = Calendario::getAnyoAcademico();
        if (!$anyo) {
            $anyo = $anyo_academico - 1;
        }
        $planes = Plan::find()
            ->innerJoinWith('estudio')
            ->where(['estudio.anyo_academico' => $anyo_academico])  // Mostramos sólo los planes de este curso.  Alternativa: anualizar los estudios desde 2013.
            ->andWhere(['tipoEstudio_id' => [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID]])
            ->andWhere(['plan.activo' => 1])
            ->all();

        return $this->render(
            'ver-encuestas',
            [
                'anyo' => intval($anyo),
                'planes' => $planes,
            ]
        );
    }

    /**
     * Muestra un listado de los planes de doctorado,
     * con enlaces a los PDF de las encuestas de cada programa.
     */
    public function actionVerEncuestasDoct($anyo=null)
    {
        $anyo_academico = Calendario::getAnyoDoctorado();
        if (!$anyo) {
            $anyo = $anyo_academico - 1;
        }
        $planes = Plan::find()
            ->innerJoinWith('estudio')
            ->where(['estudio.anyo_academico' => $anyo_academico])  // Mostramos sólo los planes de este curso.  Alternativa: anualizar los estudios desde 2013.
            ->andWhere(['tipoEstudio_id' => [Estudio::DOCT_TIPO_ESTUDIO_ID]])
            ->andWhere(['plan.activo' => 1])
            ->all();

        return $this->render(
            'ver-encuestas-doct',
            [
                'anyo' => intval($anyo),
                'planes' => $planes,
            ]
        );
    }
}
