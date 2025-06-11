<?php

use yii\bootstrap\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Acreditación de los estudios');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
$dataProvider = new ArrayDataProvider([
    'allModels' => $acreditaciones,
    'pagination' => false,  // ['pageSize' => 10],
    'sort' => [
        'attributes' => ['nk', 'estudio.nombre', 'fecha_acreditacion'],
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
        'nk',
        [
            'attribute' => 'estudio.nombre',
            'label' => Yii::t('models', 'Nombre'),
            'value' => 'estudio.nombre',
        ],
        [
            'label' => 'IU',
            'value' => 'esInteruniversitario',
            'format' => 'boolean',
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Es interuniversitario'),
            ],
        ],
        [
            'label' => 'cUZ',
            'value' => 'coordinaUz',
            'format' => 'boolean',
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Coordina UZ'),
            ],
        ],
        'fecha_verificacion',
        'fecha_implantacion',
        [
            'label' => 'Fecha de última renovación',
            'attribute' => 'fechaAcreditacion',
        ],
        'anyos_validez',
        [
            'label' => Yii::t('cati', 'Centro acreditado'),
            'value' => function ($acreditacion) {
                $centro = $acreditacion->getCentroAcreditado();
                if ($centro) {
                    return Html::a(
                        $centro->id,
                        ['ver-centro-acreditacion', 'id' => $centro->id]
                    );
                }
                return '';
            },
            'format' => 'html',
        ],
        [
            'label' => Yii::t('cruds', 'Fecha próxima renovación'),
            'value' => 'proximaRenovacion',
        ], [
            'class' => 'yii\grid\ActionColumn',
            'buttons' => [
                'actualizar-acreditacion-estudio' => function ($url, $model, $key) {
                    $options = [
                        'title' => Yii::t('gestion', 'Editar los datos'),
                        'aria-label' => Yii::t('gestion', 'Editar los datos'),
                        'data-pjax' => '0',
                    ];

                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                },
            ],
            // 'controller' => 'gestion',
            'template' => '{actualizar-acreditacion-estudio}',
            'urlCreator' => function ($action, $model, $key, $index) {
                $params = [$action, 'nk' => $model->nk];

                return Url::toRoute($params);
            },
            // visibleButtons => ...,
            'contentOptions' => ['nowrap' => 'nowrap'],
        ],
    ],
    'options' => ['class' => 'cabecera-azul'],
    // 'summary' => '', // Do not show `Showing 1-19 of 19 items'.
    'tableOptions' => [
        'class' => 'table table-striped table-bordered table-hover',
        'id' => 'tabla_acreditacion',
    ],
]);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end(); ?>

<!-- DataTables -->
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script>
<script>
    // https://datatables.net/reference/option/
    document.addEventListener("DOMContentLoaded", () => {
        $.noConflict();  // Avoid Uncaught TypeError: $(...).DataTable is not a function

        $('#tabla_acreditacion').DataTable({
            "buttons": [{ // https://datatables.net/extensions/buttons/
                className: 'btn btn-info',
                extend: 'csv',
                filename: "acreditacion_estudios",
                text: 'Descargar',
            }],
            "dom": 'Bfrtip', // https://datatables.net/reference/option/dom
            "info": false,
            // "language":
            'order': [[0, 'asc'],],
            "ordering": true,
            "paging": false,
            "searching": false,
        });

    });
</script>