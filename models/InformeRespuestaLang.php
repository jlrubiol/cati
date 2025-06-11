<?php

namespace app\models;

use Yii;
use \app\models\base\InformeRespuestaLang as BaseInformeRespuestaLang;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "informe_respuesta_lang".
 */
class InformeRespuestaLang extends BaseInformeRespuestaLang
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
