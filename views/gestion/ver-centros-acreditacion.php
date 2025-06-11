<?php

use yii\bootstrap\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Acreditación institucional de los centros');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
$dataProvider = new ArrayDataProvider([
    'allModels' => $centros,
    'pagination' => false,  // ['pageSize' => 10],
    'sort' => [
        'attributes' => ['id', 'nombre', 'municipio', 'fecha_acreditacion'],
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
    'columns' => [
        'id',
        'nombre',
        'municipio',
        'fecha_acreditacion',
        'anyos_validez',
        [
            'label' => Yii::t('cruds', 'Fecha próxima renovación'),
            'attribute' => 'proximaRenovacion',
        ], [
            'label' => Yii::t('cruds', 'URL de la acreditación institucional'),
            'value' => function ($centro) {
                return Html::a(
                    Html::encode($centro->acreditacion_url),
                    $centro->acreditacion_url
                );
            },
            'format' => 'html',
        ], [
            'class' => 'yii\grid\ActionColumn',
            'buttons' => [
                'actualizar-centro-acreditacion' => function ($url, $model, $key) {
                    $options = [
                        'title' => Yii::t('gestion', 'Editar los datos'),
                        'aria-label' => Yii::t('gestion', 'Editar los datos'),
                        'data-pjax' => '0',
                    ];

                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                },
            ],
            // 'controller' => 'gestion',
            'template' => '{actualizar-centro-acreditacion}',
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
    'tableOptions' => [
        'class' => 'table table-striped table-bordered table-hover',
        'id' => 'tabla_acreditacion_centros',
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

        $('#tabla_acreditacion_centros').DataTable({
            "buttons": [{ // https://datatables.net/extensions/buttons/
                className: 'btn btn-info',
                extend: 'csv',
                filename: "acreditacion_centros",
                text: 'Descargar',
            }],
            "dom": 'Bfrtip', // https://datatables.net/reference/option/dom
            "info": false,
            // "language":
            'order': [[1, 'asc'],],
            "ordering": true,
            "paging": false,
            "searching": false,
        });

    });
</script>
