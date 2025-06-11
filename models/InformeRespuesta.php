<?php

namespace app\models;

use app\models\base\InformeRespuesta as BaseInformeRespuesta;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "informe_respuesta".
 */
class InformeRespuesta extends BaseInformeRespuesta
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

    /* Devuelve el número de respuestas contestadas de cada informe para año e idioma */
    public function getContestadas($anyo, $language)
    {
        /*
          SELECT estudio_id, COUNT(*) AS cantidad
            FROM informe_respuesta ir
            JOIN informe_respuesta_lang irl
              ON ir.id = irl.informe_respuesta_id
           WHERE anyo = 2015
             AND language = 'es'
             AND contenido != ''
        GROUP BY estudio_id
        ;
        */
        $tabla = (new Query())
            ->select(['estudio_id', 'cantidad' => 'COUNT(*)'])
            ->from(['ir' => 'informe_respuesta'])
            ->join('INNER JOIN', ['irl' => 'informe_respuesta_lang'], 'ir.id = irl.informe_respuesta_id')
            ->where([
                'anyo' => $anyo,
                'language' => $language,
            ])->andWhere(['not in', 'contenido', ['', '<p><br></p>']])
            ->groupBy('estudio_id')
            ->all()
        ;

        $contestadas = [];
        foreach ($tabla as $fila) {
            $contestadas[$fila['estudio_id']] = $fila['cantidad'];
        }

        return $contestadas;
    }
}
