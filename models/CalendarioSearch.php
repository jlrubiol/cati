<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CalendarioSearch represents the model behind the search form about `app\models\Calendario`.
 */
class CalendarioSearch extends Calendario
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'anyo'], 'integer'],
            [['evento', 'fecha'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Calendario::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->defaultOrder = ['fecha' => SORT_DESC];
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'anyo' => $this->anyo,
            'fecha' => $this->fecha,
        ]);

        $query->andFilterWhere(['like', 'evento', $this->evento]);

        return $dataProvider;
    }
}
