<?php

namespace app\models;

use Yii;
use \app\models\base\PaimOpcion as BasePaimOpcion;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "paim_opcion".
 */
class PaimOpcion extends BasePaimOpcion
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

    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'id' => Yii::t('models', 'ID'),
                'anyo' => Yii::t('models', 'Año'),
                'campo' => Yii::t('models', 'Campo'),
                'tipo_estudio' => Yii::t('models', 'Tipo de estudio'),
                'valor' => Yii::t('models', 'Valor'),
            ]
        );
    }
}
