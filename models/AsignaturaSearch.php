<?php

namespace app\models;

use yii\base\Model;
use yii\data\ArrayDataProvider;

/**
 * Clase para filtrar los listados de asignaturas de un estudio.
 */
class AsignaturaSearch extends Model
{
    public $cursoFilter;
    public $periodoFilter;
    public $caracterFilter;
    public $idiomaFilter;
    public $estudio_id;
    public $centro_id;
    public $plan_id_nk;
    public $anyo_academico;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estudio_id', 'centro_id', 'plan_id_nk', 'anyo_academico'], 'integer'],
            [['cursoFilter', 'periodoFilter', 'caracterFilter', 'idiomaFilter', 'estudio_id', 'centro_id', 'plan_id_nk', 'anyo_academico'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     *
     * @return ArrayDataProvider
     */
    public function search($params)
    {
        $aecp = new Aecp();
        $items = [];

        // Es necesario pasarle a `load()` el parámetro `formName` como una cadena vacía,
        // porque los datos no llegan como Modelo[propiedad] => valor
        // sino simplemente como propiedad => valor .
        if ($this->load($params, '')) {
            $estudio = Estudio::getEstudio($this->estudio_id);
            if (in_array($estudio->id_nk, Estudio::FALSOS_ESTUDIO_IDS)) {
                $this->centro_id = Estudio::CENTROS_PROGRAMAS_CONJUNTOS[$estudio->id_nk];
            }

            $items = $aecp->getAsignaturas($this->estudio_id, $this->centro_id, $this->plan_id_nk, $this->anyo_academico);
            $periodoFilter = strtolower(trim($this->periodoFilter));
            $items = array_filter($items, function ($a) use ($periodoFilter) {
                if ($periodoFilter) {
                    return false !== strpos((strtolower(is_object($a) ? $a->periodo : $a['periodo'])), $periodoFilter);
                }

                return true;
            });

            $cursoFilter = $this->cursoFilter;
            $items = array_filter($items, function ($a) use ($cursoFilter) {
                return empty($cursoFilter) || ((is_object($a) ? $a->curso : $a['curso']) == $cursoFilter);
            });

            $caracterFilter = $this->caracterFilter;
            $items = array_filter($items, function ($a) use ($caracterFilter) {
                return empty($caracterFilter) || ((is_object($a) ? $a->clase : $a['clase']) == $caracterFilter);
            });

            $idiomaFilter = $this->idiomaFilter;
            $items = array_filter($items, function ($a) use ($idiomaFilter) {
                return empty($idiomaFilter) || ((is_object($a) ? $a->idioma : $a['idioma']) === $idiomaFilter);
            });
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $items,
            'pagination' => false,  // ['pageSize' => 10],
            'sort' => [
                'attributes' => ['curso', 'asignatura_id', 'periodo', 'descripcion'],
            ],
        ]);

        return $dataProvider;
    }
}
