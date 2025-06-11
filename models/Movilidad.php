<?php

namespace app\models;

use Yii;
use \app\models\base\Movilidad as BaseMovilidad;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "DATUZ_movilidad".
 * Origen: transformación `ws/titulaciones/tit_movilidad`
 * Pasarela: `json_datuz_titulaciones_movilidad.ktr`
 */
class Movilidad extends BaseMovilidad
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'ANO_ACADEMICO' => Yii::t('models', 'Año académico'),
            'COD_CENTRO' => Yii::t('models', 'Cód. centro'),
            'COD_ESTUDIO' => Yii::t('models', 'Cód. estudio'),
            'IN_SICUE' => Yii::t('models', 'SICUE'),
            'IN_ERASMUS' => Yii::t('models', 'Erasmus'),
            'IN_MOVILIDAD_VIRTUAL_UNITA' => Yii::t('models', 'Movilidad virtual UNITA'),
            'IN_MOVILIDAD_RURAL_UNITA' => Yii::t('models', ' Movilidad rural UNITA'),
            'IN_MOVILIDAD_IBEROAMERICANA' => Yii::t('models', 'Movilidad iberoamericana'),
            'IN_NOA' => Yii::t('models', 'NOA'),
            'IN_OTROS' => Yii::t('models', 'Otros'),
            'IN_TOTAL' => Yii::t('models', 'Total'),
            'OUT_SICUE' => Yii::t('models', 'SICUE'),
            'OUT_ERASMUS' => Yii::t('models', 'Erasmus'),
            'OUT_MOVILIDAD_VIRTUAL_UNITA' => Yii::t('models', 'Movilidad virtual UNITA'),
            'OUT_MOVILIDAD_IBEROAMERICANA' => Yii::t('models', 'Movilidad iberoamericana'),
            'OUT_NOA' => Yii::t('models', 'NOA'),
            'OUT_OTROS' => Yii::t('models', 'Otros'),
            'OUT_TOTAL' => Yii::t('models', 'Total'),
            'ALUMNOS_TIT_OUT' => Yii::t('models', 'Alumnos titulados'),
            'P_TITULADOS_OUT' => Yii::t('models', '% de titulados'),
        ];
    }

    /**
     * Devuelve los ID de los centros para los que hay información de movilidad de un estudio.
     */
    private static function getIdCentros($estudio)
    {
        $centro_ids = self::find()
            ->select('cod_centro')
            ->where(['cod_estudio' => $estudio->id_nk])
            ->distinct()
            ->orderBy('cod_centro')
            ->column();

        return $centro_ids;
    }

    /**
     * Devuelve los estudiantes entrantes en planes de movilidad de un estudio, clasificados por centro.
     */
    public static function getMovilidadesIn($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $anyo = $estudio->anyo_academico;
        # $centro_ids = self::getIdCentros($estudio);
        $centro_ids = array_map(function ($c) { return $c->id; }, $estudio->getCentros());

        $movilidades = [];
        foreach ($centro_ids as $centro_id) {
            $datos = self::find()
                ->select(["ANO_ACADEMICO", "IN_SICUE", "IN_ERASMUS", "IN_MOVILIDAD_VIRTUAL_UNITA", "IN_MOVILIDAD_RURAL_UNITA", "IN_MOVILIDAD_IBEROAMERICANA", "IN_NOA", "IN_OTROS", "IN_TOTAL"])
                ->where(['COD_ESTUDIO' => $estudio->id_nk, 'COD_CENTRO' => $centro_id])
                ->andWhere(['between', 'ANO_ACADEMICO', $anyo - 5, $anyo])
                ->orderBy('ANO_ACADEMICO')->asArray()->all();
            if (!$datos) continue;
            $atributos = array_keys($datos[0]);
            $todas_etiquetas = (new Movilidad())->attributeLabels();
            $etiquetas = [];
            foreach ($atributos as $atributo) {
                $etiquetas[] = $todas_etiquetas[$atributo];
            }
            $matriz = array_merge([$etiquetas], $datos);
            $datos_transpuestos = array_map(null, ...$matriz);
            $movilidades[$centro_id] = $datos_transpuestos;
        }

        return $movilidades;
    }

    /**
     * Devuelve los estudiantes salientes en planes de movilidad de un estudio, clasificados por centro.
     */
    public static function getMovilidadesOut($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $anyo = $estudio->anyo_academico;
        # $centro_ids = self::getIdCentros($estudio);
        $centro_ids = array_map(function ($c) { return $c->id; }, $estudio->getCentros());

        $movilidades = [];
        foreach ($centro_ids as $centro_id) {
            $datos = self::find()
                ->select(["ANO_ACADEMICO", "OUT_SICUE", "OUT_ERASMUS", "OUT_MOVILIDAD_VIRTUAL_UNITA", "OUT_MOVILIDAD_IBEROAMERICANA", "OUT_NOA", "OUT_OTROS", "OUT_TOTAL"])
                ->where(['COD_ESTUDIO' => $estudio->id_nk, 'COD_CENTRO' => $centro_id])
                ->andWhere(['between', 'ANO_ACADEMICO', $anyo - 5, $anyo])
                ->orderBy('ANO_ACADEMICO')->asArray()->all();
            if (!$datos) continue;
            $atributos = array_keys($datos[0]);
            $todas_etiquetas = (new Movilidad())->attributeLabels();
            $etiquetas = [];
            foreach ($atributos as $atributo) {
                $etiquetas[] = $todas_etiquetas[$atributo];
            }
            $matriz = array_merge([$etiquetas], $datos);
            $datos_transpuestos = array_map(null, ...$matriz);
            $movilidades[$centro_id] = $datos_transpuestos;
        }

        // die(var_dump($movilidades));  // DEBUG
        return $movilidades;
    }

    /**
     * Devuelve los porcentajes de titulados con estancia de movilidad internacional, clasificados por centro.
     */
    public static function getMovilidadPorcentajes($estudio_id)
    {
        $estudio = Estudio::getEstudio($estudio_id);
        $anyo = $estudio->anyo_academico;
        $centro_ids = array_map(function ($c) { return $c->id; }, $estudio->getCentros());

        $porcentajes = [];
        foreach ($centro_ids as $centro_id) {
            $datos = self::find()
                ->select(["ANO_ACADEMICO", "P_TITULADOS_OUT"])
                ->where(['COD_ESTUDIO' => $estudio->id_nk, 'COD_CENTRO' => $centro_id])
                ->andWhere(['between', 'ANO_ACADEMICO', $anyo - 5, $anyo])
                ->orderBy('ANO_ACADEMICO')->asArray()->all();
            if (!$datos) continue;
            $atributos = array_keys($datos[0]);
            $todas_etiquetas = (new Movilidad())->attributeLabels();
            $etiquetas = [];
            foreach ($atributos as $atributo) {
                $etiquetas[] = $todas_etiquetas[$atributo];
            }
            $matriz = array_merge([$etiquetas], $datos);
            $datos_transpuestos = array_map(null, ...$matriz);
            $porcentajes[$centro_id] = $datos_transpuestos;
        }

        // die(var_dump($movilidades));  // DEBUG
        return $porcentajes;
    }
}
