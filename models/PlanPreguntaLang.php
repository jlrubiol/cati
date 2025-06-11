<?php

namespace app\models;

use app\models\base\PlanPreguntaLang as BasePlanPreguntaLang;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "plan_pregunta_lang".
 */
class PlanPreguntaLang extends BasePlanPreguntaLang
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                // custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                // custom validation rules
            ]
        );
    }
}
