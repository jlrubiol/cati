<?php

namespace app\models;

use app\models\base\PlanPregunta as BasePlanPregunta;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "plan_pregunta".
 */
class PlanPregunta extends BasePlanPregunta
{
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'anyo' => Yii::t('models', 'Año'),
                'titulo' => Yii::t('models', 'Título'),
                'explicacion' => Yii::t('models', 'Explicación'),
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
                [['anyo', 'apartado', 'titulo'], 'required'],
                [['atributos', 'explicacion'], 'string'],  // Necesario para que al guardar o editar, load() cargue los datos del formulario
            ]
        );
    }
}
