<?php
/**
 * Modelo de la tabla plan_publicado.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */

namespace app\models;

use app\models\base\PlanPublicado as BasePlanPublicado;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "plan_publicado".
 */
class PlanPublicado extends BasePlanPublicado
{
    // Si se cambia este valor, hay que cambiar también el array de colores en la vista gestion/lista-planes
    const MAX_VERSION_PLAN = 3;
    const NOMBRES_GM = ['-', 'borrador provisional', 'provisional', 'definitiva'];
    const MAX_VERSION_PLAN_DOCT = 2;
    const NOMBRES_DOCT = ['-', 'provisional', 'definitiva'];

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
            ]
        );
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'anyo' => Yii::t('models', 'Año'),
                'version' => Yii::t('models', 'Versión'),
            ]
        );
    }

    public static function getNombreVersion($tipo_estudio, $num)
    {
        if ($tipo_estudio == 'grado-master') {
            if ($num >= count(self::NOMBRES_GM)) { $num = count(self::NOMBRES_GM) - 1;}
            return self::NOMBRES_GM[$num];
        } else {
            if ($num >= count(self::NOMBRES_DOCT)) { $num = count(self::NOMBRES_DOCT) - 1; }
            return self::NOMBRES_DOCT[$num];
        }
    }

    /**
     * Devuelve un array estudio_id => PlanPublicado.
     */
    public static function getPublicados($anyo, $language)
    {
        $lista = self::findAll(['anyo' => $anyo, 'language' => $language]);
        $publicados = array_combine(array_column($lista, 'estudio_id'), $lista);

        return $publicados;
    }

    /**
     * Devuelve la versión máxima del plan de mejora para el estudio dado.
     *
     * @return int Versión máxima
     */
    public function getVersionMaxima()
    {
        $estudio = Estudio::getEstudio($this->estudio_id);
        if ($estudio->esGradoOMaster()) {
            return self::MAX_VERSION_PLAN;
        } elseif ($estudio->esDoctorado()) {
            # En el curso 2021-22, PlanPublicado::MAX_VERSION_PLAN_DOCT pasó de ser 1 a 2.
            if ($estudio->anyo_academico < 2021) {
                return self::MAX_VERSION_PLAN_DOCT - 1;
            }
            return self::MAX_VERSION_PLAN_DOCT;
        }

        throw new NotFoundHttpException(sprintf(
            Yii::t('cati', 'Este tipo de estudio no tiene versión máxima del informe.  ☹')
        ));
    }
}
