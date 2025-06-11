<?php

namespace app\models;

use app\models\base\TipoEstudio as BaseTipoEstudio;
use dosamigos\translateable\TranslateableBehavior;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "tipoEstudio".
 */
class TipoEstudio extends BaseTipoEstudio
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'trans' => [
                    'class' => TranslateableBehavior::className(),
                    'translationAttributes' => [
                        'nombre',
                    ],
                ],
            ]
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(TipoEstudioLang::className(), ['tipoEstudio_id' => 'id']);
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

    /**
     * Finds the TipoEstudio model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @throws HttpException if the model cannot be found
     *
     * @param int $id
     *
     * @return TipoEstudio the loaded model
     */
    public static function getTipoEstudio($id)
    {
        if (null !== ($model = self::findOne(['id' => $id]))) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese tipo de estudio.  ☹'));
    }
}
