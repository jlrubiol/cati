<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Presidentes de las comisiones de garantía');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>


<?php
$dataProvider = new ArrayDataProvider([
    'allModels' => $presidentes,
    'pagination' => false,  // ['pageSize' => 10],
    'sort' => [
        'attributes' => ['estudio_id_nk', 'nombreEstudio', 'nombreCentro', 'plan_id_nk', 'nombre', 'apellido1', 'apellido2', 'email'],
        'defaultOrder' => [
            'nombreEstudio' => SORT_ASC,
            'nombreCentro' => SORT_ASC,
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

echo "<div class='table-responsive'>";
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'estudio_id_nk',
            'label' => Yii::t('cruds', 'Cód. estudio'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Código del estudio'),
            ],
        ], [
            'attribute' => 'nombreEstudio',
            'label' => Yii::t('cruds', 'Estudio'),
            'value' => function ($presidente) {
                return Html::a(
                    $presidente['nombreEstudio'],
                    ['estudio/ver', 'id' => $presidente['estudio_id_nk']]
                );
            },
            'format' => 'html',
        ], [
            'attribute' => 'nombreCentro',
            'label' => Yii::t('cruds', 'Centro'),
        ], [
            'attribute' => 'plan_id_nk',
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Código del plan'),
            ],
            'label' => Yii::t('cruds', 'Cód. plan'),
        ],
        'nombre',
        [
            'attribute' => 'apellido1',
            'label' => 'Primer apellido',
        ], [
            'attribute' => 'apellido2',
            'label' => 'Segundo apellido',
        ], [
            'attribute' => 'email',
            'format' => 'email', // See http://www.yiiframework.com/doc-2.0/guide-output-formatting.html
            'label' => Yii::t('cruds', 'Correo electrónico'),
        ],
    ],
    'options' => ['class' => 'cabecera-azul'],
    // 'summary' => '', // Do not show `Showing 1-19 of 19 items'.
    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
]);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end();
