<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Coordinadores de grado y máster');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
$dataProvider = new ArrayDataProvider([
    'allModels' => $planes,
    'pagination' => false,  // ['pageSize' => 10],
    'sort' => [
        'attributes' => ['estudio.id_nk', 'estudio.nombre', 'centro.nombre', 'id_nk', 'nombre_coordinador', 'email_coordinador'],
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
                    ['estudio/ver', 'id' => $plan->estudio->id_nk, 'anyo_academico' => $plan->anyo_academico]
                );
            },
            'format' => 'html',
        ], [
            'attribute' => 'centro.nombre',
            'label' => Yii::t('cruds', 'Centro'),
        ], [
            'attribute' => 'id_nk',
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Código del plan'),
            ],
        ], [
            'attribute' => 'nombre_coordinador',
            'label' => Yii::t('cruds', 'Nombre y apellidos'),
        ], [
            'attribute' => 'email_coordinador',
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
