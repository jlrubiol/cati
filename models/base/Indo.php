<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "DATUZ_INDO".
 *
 * @property integer $id
 * @property integer $COD_ESTUDIO
 * @property integer $COD_CENTRO
 * @property integer $INDO_CONVOCATORIA
 * @property integer $ANO_ACADEMICO
 * @property integer $NUM_PROYECTOS_PIET
 * @property integer $NUM_PROFESORES
 * @property integer $NUM_PROYECTOS
 * @property string $aliasModel
 */
abstract class Indo extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'DATUZ_INDO';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['COD_ESTUDIO', 'COD_CENTRO', 'INDO_CONVOCATORIA', 'ANO_ACADEMICO', 'NUM_PROYECTOS_PIET', 'NUM_PROFESORES', 'NUM_PROYECTOS'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'COD_ESTUDIO' => Yii::t('models', 'Cod  Estudio'),
            'COD_CENTRO' => Yii::t('models', 'Cod  Centro'),
            'INDO_CONVOCATORIA' => Yii::t('models', 'Indo  Convocatoria'),
            'ANO_ACADEMICO' => Yii::t('models', 'Ano  Academico'),
            'NUM_PROYECTOS_PIET' => Yii::t('models', 'Num  Proyectos  Piet'),
            'NUM_PROFESORES' => Yii::t('models', 'Num  Profesores'),
            'NUM_PROYECTOS' => Yii::t('models', 'Num  Proyectos'),
        ];
    }




}
