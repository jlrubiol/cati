<?php

namespace app\models;

use Yii;
use \app\models\base\EstudioLang as BaseEstudioLang;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "estudio_lang".
 */
class EstudioLang extends BaseEstudioLang
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
