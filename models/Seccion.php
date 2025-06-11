<?php

namespace app\models;

use Yii;
use \app\models\base\Seccion as BaseSeccion;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "seccion".
 */
class Seccion extends BaseSeccion
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
