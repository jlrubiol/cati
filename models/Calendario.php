<?php

namespace app\models;

use Yii;
use app\models\base\Calendario as BaseCalendario;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "calendario".
 */
class Calendario extends BaseCalendario
{
    const EVENTOS = [
        'publicacion_doa' => 'Cambio año Grado y Máster',
        'comienzo_anyo_academico_doct' => 'Cambio año Doctorado',
        'publicacion_informes_doct' => 'Publicación informes de Doctorado',
    ];

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
                [['evento', 'anyo', 'fecha'], 'required'],
            ]
        );
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'anyo' => Yii::t('models', 'Año'),
            ]
        );
    }

    /**
     * Devuelve el año académico actual, en base a la fecha de publicación de la
     * Definición de la Oferta Académica (hacia primeros de julio).
     */
    public static function getAnyoAcademico()
    {
        $pub_doa = self::find()->where(['anyo' => date('Y'), 'evento' => 'publicacion_doa'])->one();
        $fecha_pub_doa = isset($pub_doa) ? $pub_doa->fecha : date('Y-m-d', strtotime('tomorrow'));

        return (date('Y-m-d') < $fecha_pub_doa) ? date('Y') - 1 : date('Y');
    }

    /**
     * Devuelve el año académico actual para la información sobre los estudios de Doctorado.
     */
    public static function getAnyoDoctorado()
    {
        $comienzo_anyo_academico = self::find()->where(['anyo' => date('Y'), 'evento' => 'comienzo_anyo_academico_doct'])->one();
        $fecha = isset($comienzo_anyo_academico) ? $comienzo_anyo_academico->fecha : date('Y-m-d', strtotime('tomorrow'));

        return (date('Y-m-d') < $fecha) ? date('Y') - 1 : date('Y');
    }

}
