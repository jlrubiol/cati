<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "DATUZ_estudio_previo_master".
 *
 * @property integer $id
 * @property integer $ANO_ACADEMICO
 * @property integer $COD_CENTRO
 * @property integer $COD_ESTUDIO
 * @property string $TIPO_ESTUDIO
 * @property string $COD_ESTUD_MEC_PREVIO_MASTER
 * @property string $NOMBRE_ESTUD_MEC_PREVIO_MASTER
 * @property integer $NUM_ALUMNOS_POR_ESTUDIO_PREVIO
 * @property string $A_FECHA
 * @property string $aliasModel
 */
abstract class EstudioPrevioMaster extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'DATUZ_estudio_previo_master';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ANO_ACADEMICO', 'COD_CENTRO', 'COD_ESTUDIO', 'NUM_ALUMNOS_POR_ESTUDIO_PREVIO'], 'integer'],
            [['A_FECHA'], 'safe'],
            [['TIPO_ESTUDIO'], 'string', 'max' => 3],
            [['COD_ESTUD_MEC_PREVIO_MASTER'], 'string', 'max' => 32],
            [['NOMBRE_ESTUD_MEC_PREVIO_MASTER'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'ANO_ACADEMICO' => Yii::t('models', 'Ano  Academico'),
            'COD_CENTRO' => Yii::t('models', 'Cod  Centro'),
            'COD_ESTUDIO' => Yii::t('models', 'Cod  Estudio'),
            'TIPO_ESTUDIO' => Yii::t('models', 'Tipo  Estudio'),
            'COD_ESTUD_MEC_PREVIO_MASTER' => Yii::t('models', 'Cod  Estud  Mec  Previo  Master'),
            'NOMBRE_ESTUD_MEC_PREVIO_MASTER' => Yii::t('models', 'Nombre  Estud  Mec  Previo  Master'),
            'NUM_ALUMNOS_POR_ESTUDIO_PREVIO' => Yii::t('models', 'Num  Alumnos  Por  Estudio  Previo'),
            'A_FECHA' => Yii::t('models', 'A  Fecha'),
        ];
    }




}
