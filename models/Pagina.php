<?php

namespace app\models;

use app\models\base\Pagina as BasePagina;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "pagina".
 */
class Pagina extends BasePagina
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
                // Copio las reglas de models/base/PaginaLang.php,
                // porque Yii no las coge automágicamente.
                // Necesarias para la validación en los formularios de creación
                // y actualización, y para en el controlador poder asignar masivamente
                // los atributos con load($_POST) y guardar con save().
                [['cuerpo'], 'string'],
                [['language'], 'string', 'max' => 5],
                [['titulo'], 'string', 'max' => 255],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'language' => Yii::t('models', 'Idioma'),
                'titulo' => Yii::t('models', 'Título'),
            ]
        );
    }
}
