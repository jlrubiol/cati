<?php

namespace app\models;

use Yii;
use \app\models\base\Cifice as BaseCifice;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "DATUZ_CIFICE".
 * Los datos proceden de la transformación XXX
 */
class Cifice extends BaseCifice
{
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'cod_centro' => Yii::t('models', 'Cód. centro'),
            'cod_estudio' => Yii::t('models', 'Cód. estudio'),
            'denom_estudio' => Yii::t('models', 'Denominación del estudio'),
            'cursos_cifice' => Yii::t('models', 'Número de cursos realizados'),
            'participantes_cifice' => Yii::t('models', 'Número de profesores participantes'),
            'ano_academico' => Yii::t('models', 'Año académico'),
        ];
    }

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

    /**
     * Devuelve los datos de los cursos CIFICE, por centro.
     */
    public static function getCifices($anyo, $estudio_id_nk)
    {
        $estudio = Estudio::findOne(['id_nk' => $estudio_id_nk, 'anyo_academico' => $anyo]);
        $centro_ids = array_map(function ($c) { return $c->id; }, $estudio->getCentros());
        $cifices = [];

        foreach ($centro_ids as $centro_id) {
            $datos = self::find()
                ->select(['ano_academico', 'cursos_cifice', 'participantes_cifice'])
                ->where(['cod_estudio' => $estudio->id_nk, 'cod_centro' => $centro_id])
                ->andWhere(['between', 'ano_academico', $anyo - 5, $anyo])
                ->orderBy('ano_academico')
                ->asArray()->all();
            if (!$datos) continue;
            $atributos = array_keys($datos[0]);
            $todas_etiquetas = (new Cifice())->attributeLabels();
            $etiquetas = array_map(function ($a) use ($todas_etiquetas) { return $todas_etiquetas[$a]; }, $atributos);
            $matriz = array_merge([$etiquetas], $datos);
            $datos_transpuestos = array_map(null, ...$matriz);
            $cifices[$centro_id] = $datos_transpuestos;
        }

        return $cifices;
    }
}
