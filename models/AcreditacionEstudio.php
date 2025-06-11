<?php

namespace app\models;

use Yii;
use \app\models\base\AcreditacionEstudio as BaseAcreditacionEstudio;
use yii\helpers\ArrayHelper;
use app\models\Estudio;

/**
 * This is the model class for table "acreditacion_estudio".
 */
class AcreditacionEstudio extends BaseAcreditacionEstudio
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

    /**
     * Finds the AcreditacionEstudio model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @throws HttpException if the model cannot be found
     *
     * @param int $id
     *
     * @return AcreditacionEstudio the loaded model
     */
    public static function getAcreditacion($nk)
    {
        if (null !== ($model = self::findOne(['nk' => $nk]))) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('cati', 'No se han encontrado datos de acreditación de ese estudio.  ☹'));
    }

    /** Devuelve el Estudio correspondiente a la acreditación para el último curso académico */
    public function getEstudio()
    {
        return Estudio::find()->where(['id_nk' => $this->nk])->orderBy(['anyo_academico' => SORT_DESC])->limit(1)->one();
    }

    /** Devuelve la fecha de acreditación/última renovación */
    public function getFechaAcreditacion()
    {
        $centro = $this->getCentroAcreditado();
        if ($centro) {
            return $centro->fecha_acreditacion;
        }

        return $this->fecha_acreditacion;
    }


    /** Devuelve la fecha de próxima renovación */
    public function getProximaRenovacion()
    {
        $centro = $this->getCentroAcreditado();
        if ($centro) {
            return $centro->getProximaRenovacion();
        }

        $anyos = $this->anyos_validez;
        if (!$anyos) {
            return '?';
        }

        if ($this->fecha_acreditacion) {
            $ac = date_create($this->fecha_acreditacion);
            return $ac->add(date_interval_create_from_date_string("{$anyos} years"))->format('Y-m-d');
        }

        $im = date_create($this->fecha_implantacion);
        return $im->add(date_interval_create_from_date_string("{$anyos} years"))->format('Y-m-d');
    }

    /**
     * Si el estudio se imparte en algún centro acreditado, lo devuelve. Si no, False.
     */
    public function getCentroAcreditado()
    {
        $centros = $this->estudio->getCentros();
        // Se toma como centro el que haya sido acreditado más recientemente.
        usort(
            $centros,
            function ($centro1, $centro2) {
                return $centro1->fecha_acreditacion < $centro2->fecha_acreditacion;
            }
        );
        foreach ($centros as $centro) {
            if ($centro->fecha_acreditacion) {
                return $centro;
            }
        }
        return false;
    }

    /**
     * Devuelve si alguno de los planes del estudio es interuniversitario.
     */
    public function getEsInteruniversitario()
    {
        foreach ($this->estudio->plans as $plan) {
            if ($plan->es_interuniversitario) return true;
        }
        return false;
    }

    /**
     * Para los estudios interuniversitarios, devuelve si lo coordina la Universidad de Zaragoza.
     */
    public function getCoordinaUz()
    {
        if (!$this->getEsInteruniversitario()) return null;

        foreach ($this->estudio->plans as $plan) {
            if ($plan->coordina_uz) return true;
        }
        return false;
    }
}
