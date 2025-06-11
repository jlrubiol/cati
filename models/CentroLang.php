<?php

namespace app\models;

use Yii;
use \app\models\base\CentroLang as BaseCentroLang;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "centro_lang".
 */
class CentroLang extends BaseCentroLang
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
