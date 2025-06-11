<?php

namespace app\models;

use app\models\base\Informacion as BaseInformacion;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "informacion".
 */
class Informacion extends BaseInformacion
{
    // Estas secciones las modifica la Unidad de Grado y Máster/Escuela de Doctorado, no los coordinadores.
    const SECCIONES_RESTRINGIDAS = [
        22, 23, 25, 26, 55, 61, 62, 63, 64,
        721,  // Competencias básicas
        722,  // Capacidades y destrezas personales
        731,  // Información general
        774,  // Periodo y procedimiento de matriculación
        741,  // Supervisión de tesis
        742,  // Seguimiento del doctorando
        751,  // Formación transversal
        775,  // Movilidad
        779,  // Normativa académica
        780,  // Duración de los estudios y permanencia
        781,  // Calendario académico
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
            ]
        );
    }
}
