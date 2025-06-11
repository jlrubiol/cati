<?php

namespace app\models;

use app\models\base\AcreditacionTitulaciones as BaseAcreditacionTitulaciones;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "DATUZ_acreditacion_titulaciones".
 */
class AcreditacionTitulaciones extends BaseAcreditacionTitulaciones
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                // custom behaviors
            ]
        );
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'TASA_EFICIENCIA' => Yii::t('models', 'Tasa  Eficiencia'),
            'ALUMNOS_MATRICULADOS' => Yii::t('models', 'Alumnos  Matriculados'),
            'CREDITOS_RECONOCIDOS' => Yii::t('models', 'Creditos  Reconocidos'),
            'TIPO_ESTUDIO' => Yii::t('models', 'Tipo  Estudio'),
            'ALUMNOS_GRADUADOS' => Yii::t('models', 'Alumnos  Graduados'),
            'TASA_GRADUACION' => Yii::t('models', 'Tasa  Graduacion'),
            'PLAZAS_OFERTADAS' => Yii::t('models', 'Plazas  Ofertadas'),
            'DURACION_MEDIA_GRADUADOS' => Yii::t('models', 'Duracion  Media  Graduados'),
            'ALUMNOS_ADAPTA_GRADO_TITULADO' => Yii::t('models', 'Alumnos  Adapta  Grado  Titulado'),
            'TASA_ABANDONO' => Yii::t('models', 'Tasa  Abandono'),
            'TASA_EXITO' => Yii::t('models', 'Tasa  Exito'),
            'COD_CENTRO' => Yii::t('models', 'Cod  Centro'),
            'DENOM_ESTUDIO' => Yii::t('models', 'Denom  Estudio'),
            'ALUMNOS_NUEVO_INGRESO' => Yii::t('models', 'Alumnos  Nuevo  Ingreso'),
            'ALUMNOS_ADAPTA_GRADO_MATRI' => Yii::t('models', 'Alumnos  Adapta  Grado  Matri'),
            'TASA_RENDIMIENTO' => Yii::t('models', 'Tasa  Rendimiento'),
            'FECHA_CARGA' => Yii::t('models', 'Fecha  Carga'),
            'ALUMNOS_ADAPTA_GRADO_MATRI_NI' => Yii::t('models', 'Alumnos  Adapta  Grado  Matri  Ni'),
            'ANO_ACADEMICO' => Yii::t('models', 'Año académico'),
            'ALUMNOS_CON_RECONOCIMIENTO' => Yii::t('models', 'Alumnos  Con  Reconocimiento'),
            'CREDITOS_MATRICULADOS' => Yii::t('models', 'Creditos  Matriculados'),
            'COD_ESTUDIO' => Yii::t('models', 'Cód. estudio'),
            'ALUMNOS_MOVILIDAD_SALIDA' => Yii::t('models', 'Estudiantes enviados'),
            'ALUMNOS_MOVILIDAD_ENTRADA' => Yii::t('models', 'Estudiantes acogidos'),
        ];
    }

    public static function getDpMovilidades($anyo, $estudio_id_nk)
    {
        $query = self::find()
            ->where(['COD_ESTUDIO' => $estudio_id_nk])
            ->andWhere(['ANO_ACADEMICO' => $anyo]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }
}
