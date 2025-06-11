<?php

namespace app\models;

use app\models\base\AsignaturaCalificacion as BaseAsignaturaCalificacion;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "DATUZ_asignatura_calificacion".
 */
class AsignaturaCalificacion extends BaseAsignaturaCalificacion
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

    public static function getDpsCalificaciones($anyo, $estudio)
    {
        $centros = $estudio->getCentros();
        $dpsCalificaciones = [];
        foreach ($centros as $centro) {
            $dpsCalificaciones[$centro->id] = self::getDpCalificaciones($anyo, $centro->id, $estudio->id_nk);
        }

        return $dpsCalificaciones;
    }

    public static function getDpCalificaciones($anyo, $centro_id, $estudio_id_nk)
    {
        $query = (new Query())
            ->select([
                'PRELA_CU',
                'COD_ASIGNATURA',
                'DENOM_ASIGNATURA',
                'ALUMNOS_NO_PRESENTADOS' => 'SUM(ALUMNOS_NO_PRESENTADOS)',
                'ALUMNOS_SUSPENDIDOS' => 'SUM(ALUMNOS_SUSPENDIDOS)',
                'ALUMNOS_APROBADOS' => 'SUM(ALUMNOS_APROBADOS)',
                'TOTAL_ALUMNOS' => 'SUM(ALUMNOS_NO_PRESENTADOS + ALUMNOS_SUSPENDIDOS + ALUMNOS_APROBADOS)',
                'NUMERO_APROBADO' => 'SUM(NUMERO_APROBADO)',
                'NUMERO_NOTABLE' => 'SUM(NUMERO_NOTABLE)',
                'NUMERO_SOBRESALIENTE' => 'SUM(NUMERO_SOBRESALIENTE)',
                'NUMERO_MATRICULA_HONOR' => 'SUM(NUMERO_MATRICULA_HONOR)',
                'NUMERO_OTRO' => 'SUM(NUMERO_OTRO)',
                'A_FECHA',
            ])
            ->from('DATUZ_asignatura_calificacion')
            ->where([
                'COD_ESTUDIO' => $estudio_id_nk,
                'ANO_ACADEMICO' => $anyo,
                'COD_CENTRO' => $centro_id,
            ])
            ->groupBy('PRELA_CU, COD_ASIGNATURA, DENOM_ASIGNATURA, A_FECHA')
        ;
        // $command = $query->createCommand();
        // die($command->rawSql);  // returns the actual SQL
        $dataProvider = new ActiveDataProvider([
            'pagination' => false,
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'PRELA_CU' => SORT_ASC,
                    'COD_ASIGNATURA' => SORT_ASC,
                ],
                'attributes' => [
                    'PRELA_CU',
                    'COD_ASIGNATURA',
                    'DENOM_ASIGNATURA',
                ],
            ],
        ]);

        return $dataProvider;
    }
}
