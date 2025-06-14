<?php

namespace app\models;

use Yii;
use \app\models\base\PaginaLang as BasePaginaLang;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "pagina_lang".
 */
class PaginaLang extends BasePaginaLang
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
