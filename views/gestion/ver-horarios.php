<?php

use yii\bootstrap\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Horarios');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/grado-master']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?> <small><?php echo "{$anyo}/{$siguiente_anyo}" ?></small></h1>
<hr><br>

<?php
$dataProvider = new ArrayDataProvider([
    'allModels' => $planes,
    'pagination' => false,  // ['pageSize' => 10],
    'sort' => [
        'attributes' => ['estudio.id_nk', 'estudio.nombre', 'centro.nombre', 'id_nk'],
        'defaultOrder' => [
            'estudio.nombre' => SORT_ASC,
            'centro.nombre' => SORT_ASC,
        ],
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

echo "<div align='right'>";
echo Html::a('<i class="glyphicon glyphicon-repeat"></i>  '.Yii::t('gestion', 'Cargar URL de horarios del año anterior'),
   ['gestion/cargar-url-horarios-anterior',],
   ['class'=>'btn btn-primary'],
   ['style' => ['text-align' => 'right',]],
);
echo "</div>";

echo "<div class='table-responsive'>";
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'estudio.id_nk',
                'label' => Yii::t('cruds', 'Cód. estudio'),
                'headerOptions' => [  // HTML attributes for the header cell tag
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'title' => Yii::t('gestion', 'Código del estudio'),
                ],
            ], [
                'attribute' => 'estudio.nombre',
                'label' => Yii::t('cruds', 'Estudio'),
                'value' => function ($plan) {
                    return Html::a(
                        $plan->estudio->nombre,
                        ['estudio/ver', 'id' => $plan->estudio->id_nk]
                    );
                },
                'format' => 'html',
            ], [
                'attribute' => 'centro.nombre',
                'label' => Yii::t('cruds', 'Centro'),
            ], [
                'attribute' => 'id_nk',
                'label' => Yii::t('cruds', 'Cód. plan'),
                'headerOptions' => [  // HTML attributes for the header cell tag
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'title' => Yii::t('gestion', 'Código del plan'),
                ],
            ], [
                'label' => Yii::t('cruds', 'URL del horario'),
                'value' => function ($plan) {
                    return Html::a(
                        $plan->url_horarios,
                        $plan->url_horarios
                    );
                },
                'format' => 'html',
            ], [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'actualizar-horario' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('gestion', 'Editar la dirección'),
                            'aria-label' => Yii::t('gestion', 'Editar la dirección'),
                            'data-pjax' => '0',
                        ];

                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                    },
                ],
                // 'controller' => 'gestion',
                'template' => '{actualizar-horario}',
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
