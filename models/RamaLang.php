<?php

namespace app\models;

use Yii;
use \app\models\base\RamaLang as BaseRamaLang;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "rama_lang".
 */
class RamaLang extends BaseRamaLang
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
