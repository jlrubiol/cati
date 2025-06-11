<?php

namespace app\models;

use Yii;
use \app\models\base\Indo as BaseIndo;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "DATUZ_INDO".
 */
class Indo extends BaseIndo
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
            'id' => Yii::t('models', 'ID'),
            'COD_ESTUDIO' => Yii::t('models', 'Cód. estudio'),
            'COD_CENTRO' => Yii::t('models', 'Cód. centro'),
            'INDO_CONVOCATORIA' => Yii::t('models', 'Convocatoria'),
            'ANO_ACADEMICO' => Yii::t('models', 'Año académico'),
            'NUM_PROYECTOS_PIET' => Yii::t('models', ' Nº de proyectos PIET (Innovación Estratégica de la Titulación) aprobados'),
            'NUM_PROFESORES' => Yii::t('models', 'Nº de profesores del estudio que han participado en proyectos de innovación'),
            'NUM_PROYECTOS' => Yii::t('models', 'Nº de proyectos de innovación en los que han participado los profesores del estudio'),
        ];
    }

    /**
     * Devuelve los datos de participación en proyectos de Innovación Docente, por centro.
     */
    public static function getIndos($anyo, $estudio_id_nk)
    {
        $estudio = Estudio::findOne(['id_nk' => $estudio_id_nk, 'anyo_academico' => $anyo]);
        $centro_ids = array_map(function ($c) { return $c->id; }, $estudio->getCentros());
        $indos = [];

        foreach ($centro_ids as $centro_id) {
            $datos = self::find()
                ->select(['INDO_CONVOCATORIA', 'NUM_PROYECTOS', 'NUM_PROYECTOS_PIET', 'NUM_PROFESORES'])
                ->where(['COD_ESTUDIO' => $estudio->id_nk, 'COD_CENTRO' => $centro_id])
                ->andWhere(['between', 'INDO_CONVOCATORIA', $anyo - 5, $anyo])
                ->orderBy('INDO_CONVOCATORIA')
                ->asArray()->all();
            if (!$datos) continue;
            $atributos = array_keys($datos[0]);
            $todas_etiquetas = (new Indo())->attributeLabels();
            $etiquetas = array_map(function ($a) use ($todas_etiquetas) { return $todas_etiquetas[$a]; }, $atributos);
            $matriz = array_merge([$etiquetas], $datos);
            $datos_transpuestos = array_map(null, ...$matriz);
            $indos[$centro_id] = $datos_transpuestos;
        }

        return $indos;
    }
}
