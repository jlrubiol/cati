<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "pagina_lang".
 *
 * @property integer $id
 * @property integer $pagina_id
 * @property string $language
 * @property string $titulo
 * @property string $cuerpo
 *
 * @property \app\models\Pagina $pagina
 * @property string $aliasModel
 */
abstract class PaginaLang extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pagina_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pagina_id'], 'integer'],
            [['cuerpo'], 'string'],
            [['language'], 'string', 'max' => 5],
            [['titulo'], 'string', 'max' => 255],
            [['pagina_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Pagina::className(), 'targetAttribute' => ['pagina_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'pagina_id' => Yii::t('models', 'Pagina ID'),
            'language' => Yii::t('models', 'Language'),
            'titulo' => Yii::t('models', 'Titulo'),
            'cuerpo' => Yii::t('models', 'Cuerpo'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagina()
    {
        return $this->hasOne(\app\models\Pagina::className(), ['id' => 'pagina_id']);
    }




}
