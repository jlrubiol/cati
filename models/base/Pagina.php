<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;
use dosamigos\translateable\TranslateableBehavior;

/**
 * This is the base-model class for table "pagina".
 *
 * @property integer $id
 * @property string $aliasModel
 */
abstract class Pagina extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pagina';
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
                    'cuerpo'
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
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(\app\models\PaginaLang::className(), ['pagina_id' => 'id']);
    }



}
