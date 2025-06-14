<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;
use dosamigos\translateable\TranslateableBehavior;

/**
 * This is the base-model class for table "informacion".
 *
 * @property integer $id
 * @property integer $estudio_id
 * @property integer $seccion_id
 *
 * @property \app\models\Estudio $estudio
 * @property \app\models\Seccion $seccion
 * @property string $aliasModel
 */
abstract class Informacion extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'informacion';
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'translatable' => [
                'class' => TranslateableBehavior::className(),
                // in case you renamed your relation, you can setup its name
                // 'relation' => 'translations',
                'translationAttributes' => [
                    'titulo',
                    'texto'
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['estudio_id', 'seccion_id'], 'integer'],
            [['estudio_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Estudio::className(), 'targetAttribute' => ['estudio_id' => 'id']],
            [['seccion_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Seccion::className(), 'targetAttribute' => ['seccion_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'estudio_id' => Yii::t('models', 'Estudio ID'),
            'seccion_id' => Yii::t('models', 'Seccion ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstudio()
    {
        return $this->hasOne(\app\models\Estudio::className(), ['id' => 'estudio_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeccion()
    {
        return $this->hasOne(\app\models\Seccion::className(), ['id' => 'seccion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(\app\models\InformacionLang::className(), ['informacion_id' => 'id']);
    }



}
