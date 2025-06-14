<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "informe_publicado".
 *
 * @property integer $id
 * @property integer $estudio_id
 * @property integer $anyo
 * @property string $language
 * @property integer $version
 *
 * @property \app\models\Estudio $estudio
 * @property string $aliasModel
 */
abstract class InformePublicado extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'informe_publicado';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['estudio_id', 'anyo'], 'required'],
            [['estudio_id', 'anyo', 'version'], 'integer'],
            [['language'], 'string', 'max' => 5],
            [['estudio_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Estudio::className(), 'targetAttribute' => ['estudio_id' => 'id']]
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
            'anyo' => Yii::t('models', 'Anyo'),
            'language' => Yii::t('models', 'Language'),
            'version' => Yii::t('models', 'Version'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstudio()
    {
        return $this->hasOne(\app\models\Estudio::className(), ['id' => 'estudio_id']);
    }
}
