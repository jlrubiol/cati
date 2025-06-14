<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "equipo".
 *
 * @property integer $id
 * @property integer $estudio_id
 * @property integer $orden
 * @property string $nombre_equipo
 * @property string $nombre
 * @property string $apellido1
 * @property string $apellido2
 * @property string $institucion
 * @property integer $estudio_id_nk
 * @property integer $NIP
 * @property string $URL_CV
 * @property string $aliasModel
 */
abstract class Equipo extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['estudio_id', 'orden', 'estudio_id_nk', 'NIP'], 'integer'],
            [['nombre_equipo', 'institucion'], 'string', 'max' => 255],
            [['nombre', 'apellido1', 'apellido2'], 'string', 'max' => 31],
            [['URL_CV'], 'string', 'max' => 256]
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
            'orden' => Yii::t('models', 'Orden'),
            'nombre_equipo' => Yii::t('models', 'Nombre Equipo'),
            'nombre' => Yii::t('models', 'Nombre'),
            'apellido1' => Yii::t('models', 'Apellido1'),
            'apellido2' => Yii::t('models', 'Apellido2'),
            'institucion' => Yii::t('models', 'Institucion'),
            'estudio_id_nk' => Yii::t('models', 'Estudio Id Nk'),
            'NIP' => Yii::t('models', 'Nip'),
            'URL_CV' => Yii::t('models', 'Url  Cv'),
        ];
    }




}
