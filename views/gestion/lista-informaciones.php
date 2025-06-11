<?php

use app\models\Estudio;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Editar información general');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
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
        'attributes' => ['nombre', 'id_nk'],
        'defaultOrder' => [
            'nombre' => SORT_ASC,
            'id_nk' => SORT_ASC,
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
            'attribute' => 'id_nk',
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Código del estudio'),
            ],
            'label' => Yii::t('gestion', 'Cód. estudio'),
        ], [
            'attribute' => 'nombre',
            'label' => Yii::t('cruds', 'Estudio'),
            'value' => function ($estudio) {
                return Html::a(
                    $estudio->nombre,
                    ['estudio/ver', 'id' => $estudio->id_nk]
                );
            },
            'format' => 'html',
        ], [
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Código del plan'),
            ],
            'label' => Yii::t('cruds', 'Cód. plan'),
            'value' => function ($estudio) {
                return implode(', ', array_column($estudio->plans, 'id_nk'));
            },
        ], [
            'label' => Yii::t('gestion', 'Información'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Editar información general'),
            ],
            'value' => function ($estudio) use ($tipo) {
                return Html::a(
                    Yii::t('gestion', 'Información'),
                    Url::to([
                        'informacion/editar-infos',
                        'estudio_id' => $estudio->id,
                        'tipo' => $tipo,
                    ])
                );
            },
            'format' => 'html',
        ],
    ],
    'options' => ['class' => 'cabecera-azul'],
    'summary' => '', // Do not show `Showing 1-19 of 19 items'.
    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
]);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end();
