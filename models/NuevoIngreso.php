<?php

namespace app\models;

use Yii;
use app\models\base\NuevoIngreso as BaseNuevoIngreso;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "DATUZ_nuevo_ingreso".
 */
class NuevoIngreso extends BaseNuevoIngreso
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
            'ANO_ACADEMICO' => Yii::t('models', 'Año académico'),
            'COD_ESTUDIO' => Yii::t('models', 'Cód. estudio'),
            'COD_CENTRO' => Yii::t('models', 'Cód. centro'),
            'EP_PAU' => Yii::t('models', 'Pruebas de acceso'),
            'EP_COU' => Yii::t('models', 'COU'),
            'EP_FP' => Yii::t('models', 'FP'),
            'EP_TITULADO' => Yii::t('models', 'Titulados'),
            'EP_MAY25' => Yii::t('models', 'Mayores de 25'),
            'EP_MAY40' => Yii::t('models', 'Mayores de 40'),
            'EP_MAY45' => Yii::t('models', 'Mayores de 45'),
            'EP_NO_CONSTA' => Yii::t('models', 'No consta'),
            'EP_EXTRANJERO' => Yii::t('models', 'Extranjero'),
            'MED_PAU' => Yii::t('models', 'Pruebas de acceso'),
            'MED_COU' => Yii::t('models', 'COU'),
            'NUM_NUEVO_INGRESO_PRI' => Yii::t('models', 'Num  Nuevo  Ingreso  Pri'),
            'MED_FP' => Yii::t('models', 'FP'),
            'MED_TITULADO' => Yii::t('models', 'Titulados'),
            'MED_MAY25' => Yii::t('models', 'Mayores de 25'),
            'MED_MAY40' => Yii::t('models', 'Mayores de 40'),
            'MED_MAY45' => Yii::t('models', 'Mayores de 45'),
            'NOTA_CORTE_1' => Yii::t('models', 'Nota de corte Pruebas de Acceso preinscripción ordinaria'),
            'NOTA_CORTE_2' => Yii::t('models', 'Nota de corte Pruebas de Acceso preinscripción extraordinaria'),
            'PLAZAS_OFERTADAS' => Yii::t('models', 'Número de plazas de nuevo ingreso'),
            'NUMERO_SOLICITUDES_1' => Yii::t('models', 'Número de preinscripciones en primer lugar'),
            'NUMERO_SOLICITUDES' => Yii::t('models', 'Número de preinscripciones'),
            'NUM_NUEVO_INGRESO' => Yii::t('models', 'Estudiantes nuevo ingreso'),
            'A_FECHA' => Yii::t('models', 'Datos a fecha'),
            'NUM_NI_COMUN_ARAGON' => Yii::t('models', 'Aragón'),
            'NUM_NI_COMUN_NO_ARAGON' => Yii::t('models', 'CCAA distinta a Aragón'),
            'NUM_NI_COMUN_NO_INFORMADO' => Yii::t('models', 'No informado'),
            'NUM_NI_PAIS_EEES' => Yii::t('models', 'País dentro del EEES'),
            'NUM_NI_PAIS_NO_EEES' => Yii::t('models', 'País fuera del EEES'),
            'NUM_NI_PAIS_NO_INFORMADO' => Yii::t('models', 'No informado'),
            'NUM_NI_SEXO_H' => Yii::t('models', 'Hombre'),
            'NUM_NI_SEXO_M' => Yii::t('models', 'Mujer'),
            'NUM_NI_SEXO_OTROS' => Yii::t('models', ' Otros'),
            'RE_18_24' => Yii::t('models', 'Menor de 25'),
            'RE_25_29' => Yii::t('models', '25-29'),
            'RE_30_34' => Yii::t('models', '30-34'),
            'RE_MAS_35' => Yii::t('models', '35 o mayor'),
        ];
    }

    public static function getDpNuevosIngresos($anyo, $estudio_id_nk)
    {
        $query = self::find()
            ->where(['COD_ESTUDIO' => $estudio_id_nk])
            ->andWhere(['ANO_ACADEMICO' => $anyo]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    /**
     * Devuelve los datos de edad de los estudiantes de un estudio, por centro.
     */
    public static function getEdades($anyo, $estudio_id_nk) {
        $edades = [];
        $nuevosIngresos = self::getNuevosIngresos($anyo, $estudio_id_nk);
        if (!$nuevosIngresos) return null;

        $requeridos = ['ANO_ACADEMICO', 'RE_18_24', 'RE_25_29', 'RE_30_34', 'RE_MAS_35'];
        foreach ($nuevosIngresos as $centro_id => $datos) {
            $edades[$centro_id] = [];
            foreach ($datos as $registro) {
                if (in_array($registro[0], $requeridos)) {
                    array_push($edades[$centro_id], array_slice($registro, 1));
                }
            }
        }

        return $edades;
    }

    /**
     * Devuelve los datos de nota media de admisión y nota de corte de los estudiantes de un estudio, por centro.
     */
    public static function getNotasMedias($anyo, $estudio_id_nk) {
        $notasMedias = [];
        $nuevosIngresos = self::getNuevosIngresos($anyo, $estudio_id_nk);
        if (!$nuevosIngresos) return null;

        $requeridos = ['ANO_ACADEMICO', 'MED_PAU', 'MED_FP', 'MED_TITULADO', 'MED_MAY25', 'MED_MAY40', 'MED_MAY45', 'NOTA_CORTE_1', 'NOTA_CORTE_2'];
        foreach ($nuevosIngresos as $centro_id => $datos) {
            $notasMedias[$centro_id] = [];
            foreach ($datos as $registro) {
                if (in_array($registro[0], $requeridos)) {
                    array_push($notasMedias[$centro_id], array_slice($registro, 1));
                }
            }
        }

        return $notasMedias;
    }

    /**
     * Devuelve los datos de nuevos ingresos de un estudio, por centro.
     */
    public static function getNuevosIngresos($anyo, $estudio_id_nk)
    {
        $nuevosIngresos = [];

        $centro_ids = self::find()
            ->select('COD_CENTRO')
            ->where(['COD_ESTUDIO' => $estudio_id_nk])
            ->andWhere(['between', 'ANO_ACADEMICO', $anyo - 5, $anyo])
            ->distinct()
            ->orderBy('cod_centro')
            ->column();

        foreach($centro_ids as $centro_id) {
            $datos = self::find()
                ->where(['COD_ESTUDIO' => $estudio_id_nk, 'COD_CENTRO' => $centro_id])
                ->andWhere(['between', 'ANO_ACADEMICO', $anyo - 5, $anyo])
                ->orderBy('ANO_ACADEMICO')->asArray()->all();
            if (!$datos) return null;
            $atributos = array_keys($datos[0]);
            $etiquetas = (new NuevoIngreso())->attributeLabels();
            $matriz = array_merge([$atributos], [$etiquetas], $datos);
            $datos_transpuestos = array_map(null, ...$matriz);
            $nuevosIngresos[$centro_id] = $datos_transpuestos;
        }

        return $nuevosIngresos;
    }

    /**
     * Devuelve los datos de estudios previos de un estudio, por centro.
     */
    public static function getEstudiosPrevios($anyo, $estudio_id_nk) {
        $estudiosPrevios = [];
        $nuevosIngresos = self::getNuevosIngresos($anyo, $estudio_id_nk);
        if (!$nuevosIngresos) return null;

        $requeridos = ['ANO_ACADEMICO', 'EP_PAU', 'EP_FP', 'EP_TITULADO', 'EP_MAY25', 'EP_MAY40', 'EP_MAY45'];
        foreach ($nuevosIngresos as $centro_id => $datos) {
            $estudiosPrevios[$centro_id] = [];
            foreach ($datos as $registro) {
                if (in_array($registro[0], $requeridos)) {
                    array_push($estudiosPrevios[$centro_id], array_slice($registro, 1));
                }
            }
        }
        return $estudiosPrevios;
    }

    /**
     * Devuelve los datos de género de los estudiantes de un estudio, por centro.
     */
    public static function getGeneros($anyo, $estudio_id_nk) {
        $generos = [];
        $nuevosIngresos = self::getNuevosIngresos($anyo, $estudio_id_nk);
        if (!$nuevosIngresos) return null;

        $requeridos = ['ANO_ACADEMICO', 'NUM_NI_SEXO_H', 'NUM_NI_SEXO_M', 'NUM_NI_SEXO_OTROS'];
        foreach ($nuevosIngresos as $centro_id => $datos) {
            $generos[$centro_id] = [];
            foreach ($datos as $registro) {
                if (in_array($registro[0], $requeridos)) {
                    array_push($generos[$centro_id], array_slice($registro, 1));
                }
            }
        }

        return $generos;
    }

    /**
     * Devuelve los datos de procedencia de los estudiantes de un estudio, por centro.
     */
    public static function getProcedencias($anyo, $estudio_id_nk) {
        $procedencias = [];
        $nuevosIngresos = self::getNuevosIngresos($anyo, $estudio_id_nk);
        if (!$nuevosIngresos) return [];

        $requeridos = ['ANO_ACADEMICO', 'NUM_NI_COMUN_ARAGON', 'NUM_NI_COMUN_NO_ARAGON', 'NUM_NI_COMUN_NO_INFORMADO'];
        foreach ($nuevosIngresos as $centro_id => $datos) {
            $procedencias[$centro_id]['CCAA'] = [];
            foreach ($datos as $registro) {
                if (in_array($registro[0], $requeridos)) {
                    array_push($procedencias[$centro_id]['CCAA'], array_slice($registro, 1));
                }
            }
        }

        $requeridos = ['ANO_ACADEMICO', 'NUM_NI_PAIS_EEES', 'NUM_NI_PAIS_NO_EEES', 'NUM_NI_PAIS_NO_INFORMADO'];
        foreach ($nuevosIngresos as $centro_id => $datos) {
            $procedencias[$centro_id]['país'] = [];
            foreach ($datos as $registro) {
                if (in_array($registro[0], $requeridos)) {
                    array_push($procedencias[$centro_id]['país'], array_slice($registro, 1));
                }
            }
        }

        return $procedencias;
    }
}
