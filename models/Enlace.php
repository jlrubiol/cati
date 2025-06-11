<?php

namespace app\models;

use app\models\base\Enlace as BaseEnlace;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "enlace".
 */
class Enlace extends BaseEnlace
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

    public function getUrls()
    {
        $enlaces = $this->find()->all();
        $cmp = function ($a, $b) {
            return $a->nombre > $b->nombre;
        };
        usort($enlaces, $cmp);
        $urls = [];
        foreach ($enlaces as $enlace) {
            $urls[] = [
                'label' => $enlace->nombre,
                'url' => $enlace->uri,
            ];
        }

        return $urls;
    }
}
