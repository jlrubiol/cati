<?php
/**
 * Modelo del plan de estudios.
 *
 * Los datos proceden de la tabla `ODSSAAS.ODS_ESTUDIO_CENTRO_PLAN`
 * por medio de la pasarela `estudios/ods_plan.ktr`.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */

namespace app\models;

use Yii;
use app\models\base\Plan as BasePlan;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "plan".
 */
class Plan extends BasePlan
{
    // En la BD se ha creado un estudio ficticio para elaborar el Informe de la
    // Calidad de los Estudios de Doctorado y de sus diferentes programas.
    const ICED_PLAN_ID = 9999;

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                // custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                // custom validation rules
                ['email_delegado', 'email'],
                ['url_horarios', 'url', 'defaultScheme' => 'http'],
                ['url_web_plan', 'url', 'defaultScheme' => 'http'],
            ]
        );
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'id_nk' => Yii::t('models', 'Cód. plan'),
                'estudio_id' => Yii::t('models', 'ID estudio'),
                'centro_id' => Yii::t('models', 'Cód. centro'),
                'creditos' => Yii::t('models', 'Créditos'),
                'duracion' => Yii::t('models', 'Duración'),
                'fecha_boe' => Yii::t('models', 'Fecha BOE'),
                'nombre_coordinador' => Yii::t('models', 'Nombre del coordinador'),
                'email_coordinador' => Yii::t('models', 'Correo electrónico del coordinador'),
                'nip_coordinador' => Yii::t('models', 'NIP del coordinador'),
                'nombre_delegado' => Yii::t('models', 'Nombre del delegado'),
                'email_delegado' => Yii::t('models', 'Correo electrónico del delegado'),
                'en_extincion' => Yii::t('models', 'En extinción'),
                'es_interuniversitario' => Yii::t('models', 'Es interuniversitario'),
                'url_horarios' => Yii::t('models', 'URL del horario'),
                'url_web_plan' => Yii::t('models', 'URL de la web específica del plan'),
                'compatible_men_esp' => Yii::t('models', 'Permite compaginar itinerarios'),
                'anyo_academico' => Yii::t('models', 'Año académico'),
                'estudio_id_nk' => Yii::t('models', 'Cód. estudio'),
            ]
        );
    }

    /**
     * Finds the Plan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @throws HttpException if the model cannot be found
     *
     * @param int $id
     *
     * @return Plan the loaded model
     */
    public static function getPlan($id)
    {
        if (null !== ($model = self::findOne(['id' => $id]))) {
            return $model;
        }

        if ($id == '9999') { // ICED
            $plan = self::findOne(['id_nk' => $id]);
            return $plan;
        }

        throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese plan.  ☹'));
    }

    /**
     * Busca un plan usando su ID nativo y año académico.
     */
    public static function getPlanByNk($anyo_academico, $id_nk)
    {
        if (null !== ($model = self::find()->where(['anyo_academico' => $anyo_academico, 'id_nk' => $id_nk])->one())) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese plan y año.  ☹'));
    }

    /**
     * Busca un plan usando su ID nativo.
     *
     * Devuelve el del último año disponible.
     */
    public static function getUltimoPlanByNk($plan_id_nk)
    {
        if (null !== ($model = self::find()->where(['id_nk' => $plan_id_nk])->orderBy(['anyo_academico' => SORT_DESC])->one())) {
            return $model;
        }

        return null;
    }

    /**
     * Devuelve los ID nativos de los planes de un estudio a partir de su ID nativo
     * No tiene en cuanta los años, y por tanto si los planes estaban activos.
     */
    public static function getListaPlanes($estudio_id_nk)
    {
        $query = self::find()
            ->select('id_nk')
            ->where(['estudio_id_nk' => $estudio_id_nk])
            ->orderBy('id_nk')
            ->distinct()
            ->asArray();
        // die($query->createCommand()->getRawSql());  // DEBUG
        $lista_planes = array_column($query->all(), 'id_nk');

        return $lista_planes;
    }

    /**
     * Devuelve un array asociativo $plan_id_nk => $nombre_centro a partir de un $estudio_id_nk
     *
     * El nombre del centro es el último en que se impartió este plan.
     */
    public static function getNombresCentros($estudio_id_nk)
    {
        $lista_planes = self::getListaPlanes($estudio_id_nk);
        $nombres_centros = [];
        foreach ($lista_planes as $id_nk) {
            $plan = self::find()->where(['id_nk' => $id_nk])->orderBy(['anyo_academico' => SORT_DESC])->one();
            $nombres_centros[$id_nk] = $plan->centro->nombre;
        }

        return $nombres_centros;
    }
}
