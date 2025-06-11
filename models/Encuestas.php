<?php

namespace app\models;

use Yii;
use \app\models\base\Encuestas as BaseEncuestas;
use \app\models\Estudio;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "DATUZ_encuestas".
 * Los datos proceden de la transformación `titulaciones/tit_encuesta_titulacion` de DATUZ
 * por medio de la pasarela `json_datuz_titulaciones_encuesta.ktr`
 */
class Encuestas extends BaseEncuestas
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }

    public function attributeLabels()
    {
        return [
            'ID_CENTRO_DOCENTE_NK' => Yii::t('models', 'Cód. centro docente'),
            'ID_CURSO_ACADEMICO_NK' => Yii::t('models', 'Año académico'),
            'ID_PLAN_ESTUDIO_NK' => Yii::t('models', 'Cód. plan'),
            'ID_ENCUESTA_NK' => Yii::t('models', 'Cód. encuesta'),
            'NOMBRE_PLAN_ESTUDIO' => Yii::t('models', 'Nombre del estudio'),
            'PORC_RESPUESTA_PLAN' => Yii::t('models', 'Tasa'),
            'MEDIA_PLAN' => Yii::t('models', 'Media'),
            'NOMBRE_ENCUESTA' => Yii::t('models', 'Nombre de la encuesta'),
            'ID_TIPO_ESTUDIO_NK' => Yii::t('models', 'Cód. tipo de estudio'),
        ];
    }

    /**
     * Devuelve las tasas de satisfacción de los estudiantes de un estudio, por centro.
     */
    public static function getEncuestas($anyo, $estudio_id_nk)
    {
        $estudio = Estudio::findOne(['id_nk' => $estudio_id_nk, 'anyo_academico' => $anyo]);
        $planes = $estudio->plans;
        $encuestas = [];

        foreach ($planes as $plan) {
            $anyos_disponibles = self::find()
                ->select('ID_CURSO_ACADEMICO_NK')
                ->where(['ID_PLAN_ESTUDIO_NK' => $plan->id_nk])
                ->andWhere(['between', 'ID_CURSO_ACADEMICO_NK', $anyo - 5, $anyo])
                ->orderBy('ID_CURSO_ACADEMICO_NK')
                ->distinct()
                ->column();
            
            $encuestas[$plan->id_nk][] = $anyos_disponibles;

            $cabecera2 =  [];  // [''];
            $num_anyos = sizeof($anyos_disponibles);
            for ($i = 0; $i < $num_anyos ; $i++) {
                $cabecera2 = array_merge($cabecera2, ['% Tasa', 'Media']);
            }
            $encuestas[$plan->id_nk][] = $cabecera2;

            $encuestas_disponibles = self::find()
                ->select('NOMBRE_ENCUESTA')
                ->where(['ID_PLAN_ESTUDIO_NK' => $plan->id_nk])
                # Custom ORDER BY
                ->orderBy(new Expression('FIELD(ID_ENCUESTA_NK, 1, 45, 2, 3, 9, 11, 26)'))
                ->distinct()
                ->column();

            foreach ($encuestas_disponibles as $nombre_encuesta) {
                $registro = [$nombre_encuesta];
                foreach($anyos_disponibles as $anyo_iteracion) {
                    $datos = self::find()
                        ->select(['PORC_RESPUESTA_PLAN', 'MEDIA_PLAN'])
                        ->where(['ID_PLAN_ESTUDIO_NK' => $plan->id_nk])
                        ->andWhere(['ID_CURSO_ACADEMICO_NK' => $anyo_iteracion])
                        ->andWhere(['NOMBRE_ENCUESTA' => $nombre_encuesta])
                        ->asArray()
                        ->all();
                    array_push($registro, $datos[0]['PORC_RESPUESTA_PLAN'] ?? null, $datos[0]['MEDIA_PLAN'] ?? null);
                }
                $encuestas[$plan->id_nk][] = $registro;
            }
        }
        return $encuestas;
    }
}
