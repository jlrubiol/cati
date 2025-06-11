<?php

use app\models\Estudio;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Resultados académicos') . ' ' . $anyo . '/' . ($anyo + 1);
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
    'pagination' => false, // ['pageSize' => 10],
    'sort' => [
        'attributes' => ['nombre', 'id'],
        'defaultOrder' => [
            'nombre' => SORT_ASC,
            'id' => SORT_ASC,
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
            'attribute' => 'nombre',
            'label' => Yii::t('cruds', 'Estudio'),
        ], [
            'attribute' => 'id_nk',
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Código del estudio'),
            ],
            'label' => Yii::t('gestion', 'Código'),
        ], [
            'label' => Yii::t('gestion', 'Movilidad'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Alumnos en planes de movilidad'),
            ],
            'value' => function ($estudio) use ($anyo) {
                return Html::a(
                    Yii::t('gestion', 'Movilidad'),
                    Url::to([
                        'informe/planes-movilidad',
                        'estudio_id' => $estudio->id,
                        'anyo' => $anyo,
                    ])
                );
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Indicadores'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Análisis de los indicadores del título'),
            ],
            'value' => function ($estudio) use ($anyo) {
                return Html::a(
                    Yii::t('gestion', 'Indicadores'),
                    Url::to([
                        'informe/indicadores',
                        'estudio_id' => $estudio->id,
                        'anyo' => $anyo,
                    ])
                );
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Calificaciones'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Distribución de calificaciones'),
            ],
            'value' => function ($estudio) use ($anyo) {
                return Html::a(
                    Yii::t('gestion', 'Calificaciones'),
                    Url::to([
                        'informe/calificaciones',
                        'estudio_id' => $estudio->id,
                        'anyo' => $anyo,
                    ])
                );
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Estudio previo'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Estudio previo de los alumnos de nuevo ingreso'),
            ],
            'value' => function ($estudio) use ($anyo) {
                return Html::a(
                    Yii::t('gestion', 'Estudio previo'),
                    Url::to([
                        'informe/estudio-previo',
                        'estudio_id' => $estudio->id,
                        'anyo' => $anyo,
                    ])
                );
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Nota media'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Nota media de admisión'),
            ],
            'value' => function ($estudio) use ($anyo) {
                return Html::a(
                    Yii::t('gestion', 'Nota media'),
                    Url::to([
                        'informe/nota-media',
                        'estudio_id' => $estudio->id,
                        'anyo' => $anyo,
                    ])
                );
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Plazas NI'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Plazas de nuevo ingreso ofertadas'),
            ],
            'value' => function ($estudio) use ($anyo) {
                return Html::a(
                    Yii::t('gestion', 'Plazas NI'),
                    Url::to([
                        'informe/plazas-nuevo-ingreso',
                        'estudio_id' => $estudio->id,
                        'anyo' => $anyo,
                    ])
                );
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Anteriores'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Resultados académicos de años anteriores'),
            ],
            'value' => function ($estudio) {
                return Html::a(
                    Yii::t('gestion', 'Anteriores'),
                    [
                        'informe/resultados-academicos',
                        'estudio_id' => $estudio->id,
                    ]
                );
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Globales'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Resultados globales'),
            ],
            'value' => function ($estudio) use ($anyo) {
                return Html::a(
                    Yii::t('gestion', 'Globales'),
                    Url::to([
                        'informe/globales',
                        'estudio_id' => $estudio->id,
                        'anyo' => $anyo,
                    ])
                );
            },
            'format' => 'html',
        ],
    ],
    'options' => ['class' => 'cabecera-azul'],
    // 'summary' => '',  // Do not show `Showing 1-19 of 19 items'.
    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
]);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end();
