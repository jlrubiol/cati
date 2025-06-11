<?php

namespace app\models;

use Yii;
use \app\models\base\InformacionLang as BaseInformacionLang;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "informacion_lang".
 */
class InformacionLang extends BaseInformacionLang
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
