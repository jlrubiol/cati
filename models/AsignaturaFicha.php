<?php

namespace app\models;

use Yii;
use yii\db\Query;
use \app\models\base\AsignaturaFicha as BaseAsignaturaFicha;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "asignatura_ficha".
 */
class AsignaturaFicha extends BaseAsignaturaFicha
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
