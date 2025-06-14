<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PaimOpcion;

/**
* PaimOpcionSearch represents the model behind the search form about `app\models\PaimOpcion`.
*/
class PaimOpcionSearch extends PaimOpcion
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'anyo'], 'integer'],
            [['campo', 'tipo_estudio', 'valor'], 'safe'],
];
}

/**
* @inheritdoc
*/
public function scenarios()
{
// bypass scenarios() implementation in the parent class
return Model::scenarios();
}

/**
* Creates data provider instance with search query applied
*
* @param array $params
*
* @return ActiveDataProvider
*/
public function search($params)
{
$query = PaimOpcion::find();

$dataProvider = new ActiveDataProvider([
'query' => $query,
]);

$this->load($params);

if (!$this->validate()) {
// uncomment the following line if you do not want to any records when validation fails
// $query->where('0=1');
return $dataProvider;
}

$query->andFilterWhere([
            'id' => $this->id,
            'anyo' => $this->anyo,
        ]);

        $query->andFilterWhere(['like', 'campo', $this->campo])
            ->andFilterWhere(['like', 'tipo_estudio', $this->tipo_estudio])
            ->andFilterWhere(['like', 'valor', $this->valor]);

return $dataProvider;
}
}