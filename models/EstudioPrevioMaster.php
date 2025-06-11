<?php

namespace app\models;

use app\models\base\EstudioPrevioMaster as BaseEstudioPrevioMaster;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "DATUZ_estudio_previo_master".
 */
class EstudioPrevioMaster extends BaseEstudioPrevioMaster
{
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
            'ANO_ACADEMICO' => Yii::t('models', 'Año académico'),
            'COD_CENTRO' => Yii::t('models', 'Cód. centro'),
            'COD_ESTUDIO' => Yii::t('models', 'Cód. estudio'),
            'TIPO_ESTUDIO' => Yii::t('models', 'Tipo estudio'),
            'COD_ESTUD_MEC_PREVIO_MASTER' => Yii::t('models', 'Cód. estudio previo'),
            'NOMBRE_ESTUD_MEC_PREVIO_MASTER' => Yii::t('models', 'Nombre del estudio previo'),
            'NUM_ALUMNOS_POR_ESTUDIO_PREVIO' => Yii::t('models', 'Número de alumnos'),
            'A_FECHA' => Yii::t('models', 'A fecha'),
            ]
        );
    }

    public static function getDpsEstudiosPrevios($anyo, $estudio_id_nk)
    {
        $dataProviders = [];
        $centro_ids = self::find()
            ->select('COD_CENTRO')
            ->where(['COD_ESTUDIO' => $estudio_id_nk, 'ANO_ACADEMICO' => $anyo])
            ->distinct()
            ->orderBy('cod_centro')
            ->column();

        foreach($centro_ids as $centro_id) {
            $query = self::find()
            ->select([
                'NOMBRE_ESTUD_MEC_PREVIO_MASTER',
                'SUM(NUM_ALUMNOS_POR_ESTUDIO_PREVIO) AS NUM_ALUMNOS_POR_ESTUDIO_PREVIO',
                'A_FECHA',
            ])->where(['COD_ESTUDIO' => $estudio_id_nk])
            ->andWhere(['ANO_ACADEMICO' => $anyo])
            ->andWhere(['COD_CENTRO' => $centro_id])
            ->groupBy('NOMBRE_ESTUD_MEC_PREVIO_MASTER, A_FECHA');

            // die(var_dump($query->createCommand()->rawSql));  // DEBUG

            $dataProvider = new ActiveDataProvider([
                'pagination' => false,
                'query' => $query,
                'sort' => [
                    'defaultOrder' => [
                        'NUM_ALUMNOS_POR_ESTUDIO_PREVIO' => SORT_DESC,
                        'NOMBRE_ESTUD_MEC_PREVIO_MASTER' => SORT_ASC,
                    ],
                    'attributes' => [
                        'NOMBRE_ESTUD_MEC_PREVIO_MASTER',
                        'NUM_ALUMNOS_POR_ESTUDIO_PREVIO',
                    ],
                ],
            ]);

            $dataProviders[$centro_id] = $dataProvider;
        }

        return $dataProviders;
    }
}
