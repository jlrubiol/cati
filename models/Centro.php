<?php

namespace app\models;

use app\models\base\Centro as BaseCentro;
use app\models\Calendario;
use app\models\Estudio;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * This is the model class for table "centro".
 *
 * Los datos proceden de la tabla `ODSSAAS.ODS_CENTRO`
 * por medio de la pasarela `estudios/ods_centro.ktr`.
 */
class Centro extends BaseCentro
{
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'direccion' => Yii::t('models', 'Dirección'),
                'telefono' => Yii::t('models', 'Teléfono'),
                'url' => Yii::t('models', 'URL'),
                'nombre_decano' => Yii::t('models', 'Nombre del decano'),
                'email_decano' => Yii::t('models', 'Email del decano'),
                'acreditacion_url' => Yii::t('models', 'URL de la acreditación'),
                'fecha_acreditacion' => Yii::t('models', 'Fecha de la acreditación/última renovación'),
                'anyos_validez' => Yii::t('models', 'Años de validez'),
            ]
        );
    }

    /**
     * Finds the Centro model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @throws HttpException if the model cannot be found
     *
     * @param int $id
     *
     * @return Centro the loaded model
     */
    public static function getCentro($id)
    {
        if (null !== ($model = self::findOne(['id' => $id]))) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese centro.  ☹'));
    }

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

    public function __toString()
    {
        return (string)$this->id;
    }

    /**
     * Devuelve los ID de los grados impartidos en el centro.
     */
    public function getGrados($anyo)
    {
        $language = Yii::$app->language;
        $query = (new Query())
            ->select([
                'e.id',
                'e.id_nk',
                'el.nombre',
            ])
            ->from(['c' => 'centro'])
            ->join(
                'INNER JOIN',
                ['p' => 'plan'],
                'c.id = p.centro_id'
            )->join(
                'INNER JOIN',
                ['e' => 'estudio'],
                'p.estudio_id = e.id'
            )->join(
                'INNER JOIN',
                ['el' => 'estudio_lang'],
                'e.id = el.estudio_id'
            )->where([
                'c.id' => $this->id,
                'e.anyo_academico' => $anyo,
                'e.tipoEstudio_id' => Estudio::GRADO_TIPO_ESTUDIO_ID,  // Grado
                'e.activo' => 1,
                'el.language' => $language,
            ])->distinct();
        $command = $query->createCommand();
        // die(var_dump($command->sql)); // returns the actual SQL
        $grados = $command->queryAll();

        return $grados;
    }

    /*
     * Devuelve los grados impartidos en cada uno de los centros.
     */
    public function getGradosPorCentro($anyo = null)
    {
        $anyo = $anyo ?: Calendario::getAnyoAcademico();
        $centros = self::find()->where(['activo' => 1])->all();
        $grados = [];
        foreach ($centros as $centro) {
            $gradosDelCentro = $centro->getGrados($anyo);
            $grados[$centro->id] = $gradosDelCentro;
        }

        return $grados;
    }

    /**
     * Devuelve los ID de los másters impartidos en el centro.
     */
    public function getMasters($anyo)
    {
        $language = Yii::$app->language;
        $query = (new Query())
            ->select([
                'e.id',
                'e.id_nk',
                'el.nombre',
            ])
            ->from(['c' => 'centro'])
            ->join(
                'INNER JOIN',
                ['p' => 'plan'],
                'c.id = p.centro_id'
            )->join(
                'INNER JOIN',
                ['e' => 'estudio'],
                'p.estudio_id = e.id'
            )->join(
                'INNER JOIN',
                ['el' => 'estudio_lang'],
                'e.id = el.estudio_id'
            )->where([
                'c.id' => $this->id,
                'e.anyo_academico' => $anyo,
                'e.tipoEstudio_id' => Estudio::MASTER_TIPO_ESTUDIO_ID, // Máster
                'e.activo' => 1,
                'el.language' => $language,
            ])->distinct();
        $command = $query->createCommand();
        // die(var_dump($command->sql)); // returns the actual SQL
        $masters = $command->queryAll();

        return $masters;
    }

    /*
     * Devuelve los másters impartidos en cada uno de los centros.
     */
    public function getMastersPorCentro($anyo = null)
    {
        $anyo = $anyo ?: Calendario::getAnyoAcademico();
        $centros = self::find()->where(['activo' => 1])->all();
        $masters = [];
        foreach ($centros as $centro) {
            $mastersDelCentro = $centro->getMasters($anyo);
            $masters[$centro->id] = $mastersDelCentro;
        }

        return $masters;
    }

    /*
     * Devuelve los estudios impartidos en cada uno de los centros.
     */
    public static function getEstudiosPorCentro($anyo = null)
    {
        $anyo = $anyo ?: Calendario::getAnyoAcademico();
        $centros = self::find()->where(['activo' => 1])->all();
        $estudios = [];
        foreach ($centros as $centro) {
            $gradosDelCentro = $centro->getGrados($anyo);
            $mastersDelCentro = $centro->getMasters($anyo);
            $estudios[] = [
                'id' => "{$centro->id}",
                'grados' => $gradosDelCentro,
                'masters' => $mastersDelCentro,
            ];
        }

        return $estudios;
    }

    /** Devuelve la fecha de próxima renovación */
    public function getProximaRenovacion()
    {
        if (!$this->fecha_acreditacion or !$this->anyos_validez) {
            return null;
        }
        $ac = date_create($this->fecha_acreditacion);
        $anyos = $this->anyos_validez;
        return $ac->add(date_interval_create_from_date_string("{$anyos} years"))->format('Y-m-d');
    }
}
