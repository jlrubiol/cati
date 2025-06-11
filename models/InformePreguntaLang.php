<?php

namespace app\models;

use Yii;
use app\models\base\InformePreguntaLang as BaseInformePreguntaLang;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "informe_pregunta_lang".
 */
class InformePreguntaLang extends BaseInformePreguntaLang
{
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'explicacion' => Yii::t('models', 'Explicación'),
                'info' => Yii::t('models', 'Información adicional'),
                'texto_comun' => Yii::t('models', 'Texto común'),
            ]
        );
    }

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
