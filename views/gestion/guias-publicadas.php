<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = sprintf(Yii::t('cati', 'Guías publicadas (curso %d-%d)'), $anyo_academico, $anyo_academico + 1);

$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>


<?php
$dataProvider = new ArrayDataProvider([
    'allModels' => $guias,
    'pagination' => ['pageSize' => 1000],  // false,  // ['pageSize' => 200],
    'sort' => ['attributes' => ['plan_id_nk', 'asignatura_id', 'fecha']],
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
            'attribute' => 'plan_id_nk',
            'label' => Yii::t('cati', 'Cód. plan'),
        ], [
            'attribute' => 'asignatura_id',  // Para poder ordenar por él
            'label' => Yii::t('cati', 'Cód. asig.'),
            'value' => function ($asignatura) {
                return Html::a(
                    $asignatura['asignatura_id'],
                    [
                        'estudio/asignatura',
                        'asignatura_id' => $asignatura['asignatura_id'],
                        'estudio_id' => $asignatura['estudio_id'],
                        'centro_id' => $asignatura['centro_id'],
                        'plan_id_nk' => $asignatura['plan_id_nk'],
                    ]
                );
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('cati', 'Nombre'),
            'value' => function ($asignatura) {
                return Html::a(
                    $asignatura['descripcion'],
                    [
                        'estudio/asignatura',
                        'asignatura_id' => $asignatura['asignatura_id'],
                        'estudio_id' => $asignatura['estudio_id'],
                        'centro_id' => $asignatura['centro_id'],
                        'plan_id_nk' => $asignatura['plan_id_nk'],
                    ]
                );
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('cati', 'Guía docente'),
            'value' => function ($asignatura) {
                return Html::a(
                    'PDF',
                    $asignatura['url_guia']
                );
            },
            'format' => 'html',
        ], [
            'attribute' => 'fecha',  // Para poder ordenar por ella
            'label' => Yii::t('cati', 'Fecha'),
            'value' => 'fecha',
            'format' => 'date',
        ],
    ],
    // 'summary' => '', // Commented to show `Showing 1-19 of 19 items'.
    'options' => ['class' => 'cabecera-azul'],
    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
]);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end();
