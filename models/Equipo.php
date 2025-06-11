<?php

namespace app\models;

use app\models\base\Equipo as BaseEquipo;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "equipo".

 * Los datos de los equipos de investigación proceden de People
 * por medio de la pasarela `equipos_investigacion.kjb`.
 */
class Equipo extends BaseEquipo
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

    public static function getNombresEquipos($estudio_id)
    {
        $query = self::find()
            ->select(['orden', 'nombre_equipo'])
            ->where(['estudio_id' => $estudio_id])
            ->orderBy('orden')
            ->distinct()
            ->asArray();
        // die($query->createCommand()->getRawSql());  // DEBUG
        $equipos = $query->all();

        return $equipos;
    }

    public static function getMiembrosEquipos($estudio_id)
    {
        return self::find()
            ->where(['estudio_id' => $estudio_id])
            ->orderBy('orden, apellido1, apellido2, nombre')
            ->all()
        ;
    }

    public function getUrlCv()
    {
        $query = (new Query())
            ->select(['c.URL',])
            ->from(['c' => 'curriculum'])
            ->where(['c.NIP' => $this->NIP])
        ;
        $command = $query->createCommand();
        $curriculum = $command->queryOne();

        if ($curriculum) {
            $slug = $curriculum['URL'];
            return sprintf('https://janovas.unizar.es/sideral/CV/%s?lang=%s', trim($slug), Yii::$app->language);
        }

        if ($this->URL_CV) {
            return $this->URL_CV;
        }

        return null;
    }
}
