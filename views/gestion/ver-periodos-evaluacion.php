<?php

use yii\bootstrap\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Periodos de evaluación de los estudios'). ' ' . $anyo . '/' . ($anyo + 1);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
$dataProvider = new ArrayDataProvider([
    'allModels' => $estudios,
    'pagination' => false,  // ['pageSize' => 10],
    'sort' => [
        'attributes' => ['id_nk', 'nombre', 'anyos_evaluacion'],
        /* 'defaultOrder' => [
            'estudio.nombre' => SORT_ASC,
        ],*/
    ],
]);

\yii\widgets\Pjax::begin(
    [
        'id' => 'pjax-main',
        'enableReplaceState' => false,
        'linkSelector' => '#pjax-main ul.pagination a, th a',
        // 'clientOptions' => ['pjax:success' => 'function() { alert("yo"); }'],
    ]
);


echo "<div class='table-responsive'>";
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'id_nk',
            'label' => Yii::t('models', 'Cód. estudio'),
        ],
        'nombre',
        'anyos_evaluacion',
        [
            'class' => 'yii\grid\ActionColumn',
            'buttons' => [
                'actualizar-periodo-evaluacion' => function ($url, $model, $key) {
                    $options = [
                        'title' => Yii::t('gestion', 'Editar los datos'),
                        'aria-label' => Yii::t('gestion', 'Editar los datos'),
                        'data-pjax' => '0',
                    ];

                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                },
            ],
            // 'controller' => 'gestion',
            'template' => '{actualizar-periodo-evaluacion}',
            'urlCreator' => function ($action, $model, $key, $index) {
                $params = [$action, 'id' => $model->id];

                return Url::toRoute($params);
            },
            // visibleButtons => ...,
            'contentOptions' => ['nowrap' => 'nowrap'],
        ],
    ],
    'options' => ['class' => 'cabecera-azul'],
    // 'summary' => '', // Do not show `Showing 1-19 of 19 items'.
    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
]);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end();
