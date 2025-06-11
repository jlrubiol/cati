<?php

namespace app\models;

use Yii;
use \app\models\base\LineaLang as BaseLineaLang;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "linea_lang".
 */
class LineaLang extends BaseLineaLang
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
