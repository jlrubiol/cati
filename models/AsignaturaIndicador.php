<?php

namespace app\models;

use yii\db\Query;
use app\models\base\AsignaturaIndicador as BaseAsignaturaIndicador;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "DATUZ_asignatura_indicador".
 */
class AsignaturaIndicador extends BaseAsignaturaIndicador
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
     * Devuelve la lista de años anteriores (a partir de un mínimo)
     * para los que hay disponible información sobre esa titulación.
     */
    public static function anyosAnteriores($estudio_id_nk, $anyo_academico)
    {
        $query = new Query();
        $query->select('ANO_ACADEMICO')
            ->from('DATUZ_asignatura_indicador')
            ->where(['COD_ESTUDIO' => $estudio_id_nk])
            ->andWhere(['BETWEEN', 'ANO_ACADEMICO', $anyo_academico - 8, $anyo_academico - 1])
            ->orderBy('ANO_ACADEMICO DESC')
            ->distinct(true);
        $anyos = $query->column();

        return $anyos;
    }
}
