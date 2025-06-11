<?php

namespace app\models;

use Yii;
use \app\models\base\NotasPlanLang as BaseNotasPlanLang;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "notasPlan_lang".
 */
class NotasPlanLang extends BaseNotasPlanLang
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
