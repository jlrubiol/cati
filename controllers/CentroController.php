<?php
/**
 * /home/quique/Devel/cati/cati/runtime/giiant/49eb2de82346bc30092f584268252ed2.
 */

namespace app\controllers;

use app\models\Calendario;
use app\models\Centro;
use Yii;

/**
 * This is the class for controller "CentroController".
 */
class CentroController extends \app\controllers\base\CentroController
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
                        'actions' => ['grados', 'masters', 'estudios', 'grados-del-centro', 'masters-del-centro'],
                        'allow' => true,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Devuelve los grados impartidos en cada uno de los centros.
     *
     * A petición de dcharro, para la página web de la universidad.
     */
    public function actionGrados()
    {
        $anyo = Calendario::getAnyoAcademico();
        $centro = new Centro();
        $grados = $centro->getGradosPorCentro($anyo);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $grados;
    }

    /**
     * Devuelve los másters impartidos en cada uno de los centros.
     *
     * A petición de dcharro, para la página web de la universidad.
     */
    public function actionMasters()
    {
        $anyo = Calendario::getAnyoAcademico();
        $centro = new Centro();
        $masters = $centro->getMastersPorCentro($anyo);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $masters;
    }

    /**
     * Devuelve los estudios impartidos en cada uno de los centros.
     *
     * A petición de dcharro, para la página web de la universidad (Drupal).
     * Se usa dentro de cada uno de los centros de https://www.unizar.es/estructura/centros
     */
    public function actionEstudios()
    {
        $anyo = Calendario::getAnyoAcademico();
        $estudios = Centro::getEstudiosPorCentro($anyo);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return ['estudios' => $estudios];
    }

    /**
     * Devuelve los grados impartidos en un centro.
     */
    public function actionGradosDelCentro($centro_id)
    {
        $anyo = Calendario::getAnyoAcademico();
        $centro = $this->findModel($centro_id);
        $grados = $centro->getGrados($anyo);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $grados;
    }

    /**
     * Devuelve los másters impartidos en un centro.
     */
    public function actionMastersDelCentro($centro_id)
    {
        $anyo = Calendario::getAnyoAcademico();
        $centro = $this->findModel($centro_id);
        $masters = $centro->getMasters($anyo);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $masters;
    }
}
