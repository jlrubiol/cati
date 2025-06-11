<?php

namespace app\models;

use Yii;
use \app\models\base\EnlaceLang as BaseEnlaceLang;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "enlace_lang".
 */
class EnlaceLang extends BaseEnlaceLang
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
