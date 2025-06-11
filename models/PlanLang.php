<?php

namespace app\models;

use Yii;
use \app\models\base\PlanLang as BasePlanLang;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "plan_lang".
 */
class PlanLang extends BasePlanLang
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
