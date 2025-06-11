<?php

namespace app\models;

use Yii;
use \app\models\base\Pas as BasePas;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "DATUZ_pas".
 * Origen: transformación `tit_pas`
 * Rellenada por la pasarela [...]
 */
class Pas extends BasePas
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

    /* Orden en que mostrar la evolución del PAS */
    const LISTA = [
        'Técnica, Mantenim. y Oficios',   // AT
        'Apoy. Doc. Inv. Lab.Arch.Bibl.', // AB
        'Administración y Svcs.Grales.',  // AG
        'Fuera RPT',                      // NO
        'No Informado',
    ];

    /**
     * Función de comparación para ordenar el array de PAS por su especialidad, según la lista superior.
     */
    private static function cmp($p1, $p2)
    {
        return array_search($p1['especialidad'], self::LISTA) - array_search($p2['especialidad'], self::LISTA);
    }

    /**
     * Devuelve las evoluciones del PAS de un estudio, clasificadas por centro.
     */
    public static function getEvolucionesPas($anyo, $estudio_id_nk)
    {
        $estudio = Estudio::findOne(['id_nk' => $estudio_id_nk, 'anyo_academico' => $anyo]);
        $centros = $estudio->getCentros();
        $evoluciones = [];

        foreach ($centros as $centro) {
            $centro_rrhh_id_nk = $centro->rrhh_id_nk;
            // El PAS de Teruel está adscrito a la Unidad Administrativa y de Servicios de Campus de Teruel (VTA)
            if ($centro->municipio == 'Teruel') {
                $centro_rrhh_id_nk = 'VTA';
            }

            // Preparamos la fila de cabecera
            $anio_meses = self::find()
                ->select('ANIO_MES_STR')
                ->where(['between', 'ANO_ACADEMICO', $anyo - 6, $anyo -1])
                ->orderBy('ANO_ACADEMICO')
                ->distinct()
                ->column();
            $evoluciones_del_centro = [array_merge(['Especialidad RPT', 'Tipo personal'], $anio_meses)];
            $anyos_disponibles = array_map(function($am) { return intval(substr($am, 0, 4)) - 1; }, $anio_meses);

            // Obtenemos las especialidad+tipo del centro
            $esp_tipos = self::find()
                ->select(['ESPECIALIDAD', 'TIPO_EMPLEADO'])
                ->where(['COD_CENTRO_RRHH' => $centro_rrhh_id_nk])
                ->andWhere(['between', 'ANO_ACADEMICO', $anyo - 6, $anyo - 1])
                ->orderBy('ESPECIALIDAD, TIPO_EMPLEADO')
                ->distinct()
                ->asArray()
                ->all();

            // Para cada especialidad+tipo obtenemos el histórico de datos
            foreach ($esp_tipos as $esp_tipo) {
                $datos_esp_tipo = self::find()
                    ->select(['ANO_ACADEMICO', 'NUM_EMPLEADOS'])
                    ->where(['COD_CENTRO_RRHH' => $centro_rrhh_id_nk])
                    ->andWhere($esp_tipo)
                    ->andWhere(['between', 'ANO_ACADEMICO', $anyo - 6, $anyo - 1])
                    ->orderBy('ANO_ACADEMICO')
                    ->asArray()
                    ->all();

                // Ponemos los datos de la esp+tipo en un array asociativo usando el año como clave
                foreach($datos_esp_tipo as $anyo_valor) {
                    $esp_tipo[$anyo_valor['ANO_ACADEMICO']] = $anyo_valor['NUM_EMPLEADOS'];
                }

                // Añadimos al array los años para los que no hubiera datos, con `0` como valor.
                foreach ($anyos_disponibles as $a) {
                    $esp_tipo[$a] = $esp_tipo[$a] ?? 0;
                }

                // Añadimos los datos de la esp+tipo a los datos del centro, y pasamos a la siguiente
                $evoluciones_del_centro[] = $esp_tipo;
            }

            // Calculamos la suma de cada columna
            $totales = ['ESPECIALIDAD' => 'Total PAS', 'TIPO_EMPLEADO' => ''];
            foreach ($anyos_disponibles as $a) {
                $totales[$a] = array_sum(array_column($evoluciones_del_centro, $a));
            }
            $evoluciones_del_centro[] = $totales;

            $evoluciones[$centro->id] = $evoluciones_del_centro;
        }

        // die(var_dump($evoluciones));  // DEBUG
        return $evoluciones;
    }
}
