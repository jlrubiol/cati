<?php

namespace app\models;

use Yii;
use \app\models\base\Linea as BaseLinea;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "linea".
 * Fuente: tablas `VEGA.TCSO_LINEAS_INVESTIGACION` etc de Sigma.
 * Vía pasarela Kettle `lineas_investigacion_doctorado.ktr`.
 */
class Linea extends BaseLinea
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
}
