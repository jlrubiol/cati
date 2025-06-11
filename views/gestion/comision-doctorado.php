<?php
/**
 * Vista de la lista de los planes de innovación y mejora para la Comisión de Doctorado.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use app\models\PlanPublicado;
use kartik\icons\Icon;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

Icon::map($this, Icon::FA); // Maps the Font Awesome icon font framework

$siguiente_anyo = $anyo + 1;
$siguesigue_anyo = $siguiente_anyo + 1;

$this->title = Yii::t('gestion', "Planes de innovación y mejora para el curso {$siguiente_anyo}/{$siguesigue_anyo} (campaña {$siguiente_anyo})");
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php

$dataProvider = new ArrayDataProvider([
    'allModels' => $datos,
    'pagination' => false,  // ['pageSize' => 10],
    'sort' => [
        'attributes' => ['id_nk', 'nombre', 'version', 'celdas'],
        'defaultOrder' => [
            'nombre' => SORT_ASC,
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
    // 'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'id_nk',
            'label' => Yii::t('gestion', 'Cód. estudio'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Código del estudio'),
            ],
        ], [
            'attribute' => 'nombre',
            'label' => Yii::t('cruds', 'Estudio'),
            'value' => function ($dato) use ($anyo) {
                return Html::a(
                    $dato['nombre'],
                    ['estudio/ver', 'id' => $dato['id_nk'], 'anyo_academico' => $anyo]
                );
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Ver/Editar'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Ver o editar el plan'),
                'style' => 'text-align: center;',
            ],
            'value' => function ($dato) use ($anyo) {
                return Html::a(
                    '<span class="glyphicon glyphicon-pencil"></span> ' .
                    Yii::t('gestion', 'Ver/Editar'),
                    ['plan-mejora/ver', 'estudio_id' => $dato['id'], 'anyo' => $anyo],
                    ['id' => 'ver-plan', 'class' => 'btn btn-info btn-xs', 'title' => Yii::t('gestion', 'Ver o editar el plan')]  // Button
                );
            },
            'contentOptions' => ['style' => 'text-align: center;'],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Versión'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Última versión publicada del plan'),
                'style' => 'text-align: center;',
            ],
            'attribute' => 'version',
            // 'filter' => '<input class="form-control" name="filterversion" value="' . $searchModel['version'] . '" type="text">',
            'contentOptions' => function ($model, $key, $index, $column) {
                $colors = ['#d9534f', '#ff8c00', '#f0ad4e', '#5cb85c']; // rojo, naranja, amarillo, verde
                $version = $model['version'];

                return [
                    'style' => 'text-align: center; background-color: ' . $colors[$version] . ';',
                    'title' => Yii::t('gestion', 'Última versión publicada del plan'),
                ];
            },
            // 'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Coordinadores'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Coordinadores del estudio'),
            ],
            'attribute' => 'coordinadores',
            // 'format' => 'html',
            'format' => 'email', // See http://www.yiiframework.com/doc-2.0/guide-output-formatting.html
        ],
    ],
    'options' => ['class' => 'cabecera-azul'],
    // 'summary' => '', // Do not show `Showing 1-19 of 19 items'.
    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
]);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end();
