<?php

namespace app\models;

use Yii;
use app\models\base\InformePregunta as BaseInformePregunta;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "informe_pregunta".
 */
class InformePregunta extends BaseInformePregunta
{
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'anyo' => Yii::t('models', 'Año'),
                'titulo' => Yii::t('models', 'Título'),
                'info' => Yii::t('models', 'URL información adicional'),
                'explicacion' => Yii::t('models', 'Explicación'),
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
                [['anyo', 'apartado', 'tipo', 'titulo'], 'required'],
                [['info', 'explicacion', 'texto_comun'], 'string'],  // Necesario para que al guardar o editar, load() cargue los datos del formulario
            ]
        );
    }
}
