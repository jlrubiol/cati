<?php

namespace app\models;

use Yii;
use \app\models\base\NotasPlan as BaseNotasPlan;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "notasPlan".
 */
class NotasPlan extends BaseNotasPlan
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
                'plan_id_nk' => Yii::t('models', 'Cód. plan'),
            ]
        );
    }

    public static function getNotasPlan($id_nk)
    {
        if (null !== ($model = self::findOne(['plan_id_nk' => $id_nk]))) {
            return $model;
        }

        return new NotasPlan(['plan_id_nk' => $id_nk]);
    }
}
