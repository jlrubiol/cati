<?php

namespace app\models;

use Yii;
use \app\models\base\TipoEstudioLang as BaseTipoEstudioLang;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tipoEstudio_lang".
 */
class TipoEstudioLang extends BaseTipoEstudioLang
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
