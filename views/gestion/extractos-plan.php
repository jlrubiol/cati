<?php
/**
 * Vista de la lista de las acciones de un apartado de los planes de innovación y mejora.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

$this->title = $pregunta->apartado . ' ' . $pregunta->titulo;
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Planes') . ' ' . $anyo . '/' . ($anyo + 1),
    'url' => ['gestion/lista-planes', 'anyo' => $anyo, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Acciones'),
    'url' => ['gestion/seleccionar-pregunta-plan', 'anyo' => $anyo, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);

$css = <<<CSS
@media print {
    body {
        font-size: 12pt;
        margin-right: 0cm;
        margin-left: 0cm;
    }
}
#contenedor-principal {
    width: 1536px; /* 1440px;*/
}
CSS;

$this->registerCss($css);

?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php

if ($pregunta->explicacion) {
    echo "<p class='alert alert-info'>\n";
    echo "<span class='glyphicon glyphicon-info-sign'></span> " . HtmlPurifier::process($pregunta->explicacion) . "\n";
    echo "</p>\n";
}

$campos_pregunta = array_map(
    function ($a) { return trim($a); },
    explode(',', $pregunta->atributos)
);

if (in_array('valores_a_alcanzar', $campos_pregunta)) {
    array_push($campos_pregunta, 'valores_alcanzados');
}

$dataProvider = new ArrayDataProvider([
    'allModels' => $respuestas,
    'pagination' => false,  // ['pageSize' => 20],
    'sort' => [
        'attributes' => ['ambito_id', 'necesidad_detectada', 'objetivo'],
        'defaultOrder' => [
            'ambito_id' => SORT_ASC,
            'necesidad_detectada' => SORT_ASC,
            'objetivo' => SORT_ASC
        ]
    ],
]);

echo "<div class='table-responsive'>";

echo GridView::widget(
    [
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            // 'attribute' => 'estudio.centroAcreditado.id',
            'label' => 'Centro acreditado',
            'value' => function ($pr) {
                return $pr->estudio->getCentroAcreditado() ? $pr->estudio->getCentroAcreditado()->id : '';
            },
        ], [
            'attribute' => 'estudio.nombre',
            'label' => 'Estudio',
        ], [
            'attribute' => 'id',
            // En la sección de «Fecha aprobación CGC» no mostramos el ID.
            'visible' => ($pregunta->atributos != 'fecha'),
            # 'attribute' => 'codigo',  # Para seguir el patrón decidido por el Vicerrectorado
        ], [
            'attribute' => 'ambito',
            'visible' => in_array('ambito_id', $campos_pregunta),
            'value' => function ($respuesta) { return $respuesta->ambito['valor'] ?? null; },
            'headerOptions' => ['class' => 'col-sm-1'],
        ], [
            'attribute' => 'necesidad_detectada',
            'visible' => in_array('necesidad_detectada', $campos_pregunta),
            'headerOptions' => ['class' => 'col-sm-2'],
        ], [
            'attribute' => 'objetivo',
            'visible' => in_array('objetivo', $campos_pregunta),
            'headerOptions' => ['class' => 'col-sm-2'],
        ], [
            'attribute' => 'apartado_memoria',
            'visible' => in_array('apartado_memoria_id', $campos_pregunta),
            'value' => function ($respuesta) { return $respuesta->apartadoMemoria['valor'] ?? null; },
            'headerOptions' => ['class' => 'col-sm-1'],
        ], [
            'attribute' => 'tipo_modificacion',
            'visible' => in_array('tipo_modificacion_id', $campos_pregunta),
            'value' => function ($respuesta) { return $respuesta->tipoModificacion['valor'] ?? null; },
            'headerOptions' => ['class' => 'col-sm-1'],
        ], [
            'attribute' => 'titulo',
            'visible' => in_array('titulo', $campos_pregunta),
            'headerOptions' => ['class' => 'col-sm-2'],
        ], [
            'attribute' => 'descripcion_breve',
            'visible' => in_array('descripcion_breve', $campos_pregunta),
            'headerOptions' => ['class' => 'col-sm-2'],
        ], [
            'attribute' => 'descripcion_amplia',
            'visible' => in_array('descripcion_amplia', $campos_pregunta),
            'headerOptions' => ['class' => 'col-sm-5'],
        ], [
            'label' => Yii::t('cati', 'Resp. acción y seguimiento'),
            'attribute' => 'responsable_accion',
            'visible' => in_array('responsable_accion', $campos_pregunta),
            'headerOptions' => ['class' => 'col-sm-2'],
        ], [
            'attribute' => 'problema',
            'visible' => in_array('problema', $campos_pregunta),
        ], [
            'attribute' => 'acciones',
            'visible' => in_array('acciones', $campos_pregunta),
        ], [
            'attribute' => 'inicio',
            'visible' => in_array('inicio', $campos_pregunta),
        ], [
            'attribute' => 'final',
            'visible' => in_array('final', $campos_pregunta),
        ], [
            'attribute' => 'responsable_competente',
            'visible' => in_array('responsable_competente', $campos_pregunta),
            'headerOptions' => ['class' => 'col-sm-2'],
        ], [
            'attribute' => 'responsable_aprobacion',
            'visible' => in_array('responsable_aprobacion_id', $campos_pregunta),
            'value' => function ($respuesta) { return $respuesta->responsableAprobacion['valor'] ?? null; },
        ], [
            'attribute' => 'plazo_implantacion',
            'visible' => in_array('plazo_implantacion', $campos_pregunta),
        ], [
            'attribute' => 'plazo',
            'visible' => in_array('plazo_id', $campos_pregunta),
            'value' => function ($respuesta) { return $respuesta->plazo['valor'] ?? null; },
        ],[
            'attribute' => 'indicador',
            'visible' => in_array('indicador', $campos_pregunta),
            // 'headerOptions' => ['class' => 'col-sm-2'],
        ], [
            'attribute' => 'valores_a_alcanzar',
            'visible' => in_array('valores_a_alcanzar', $campos_pregunta),
            'headerOptions' => ['class' => 'col-sm-2'],
        ], [
            'attribute' => 'valores_alcanzados',
            'visible' => in_array('valores_alcanzados', $campos_pregunta),
        ], [
            'attribute' => 'justificacion_breve',
            'visible' => in_array('justificacion_breve', $campos_pregunta),
        ], [
            'attribute' => 'observaciones',
            # 'visible' => in_array('observaciones', $campos_pregunta),
        ], [
            'attribute' => 'cumplimiento',
            'visible' => in_array('cumplimiento', $campos_pregunta),
        ], [
            'attribute' => 'justificacion',
            'visible' => in_array('justificacion', $campos_pregunta),
        ], [
            'attribute' => 'nivel',
            'visible' => in_array('nivel', $campos_pregunta),
        ], [
            'attribute' => 'fecha',
            'visible' => in_array('fecha', $campos_pregunta),
        ], [
            'attribute' => 'estado',
            'value' => function ($respuesta) { return $respuesta->estado['valor'] ?? null; },
        ], [
            'attribute' => 'version',
            'label' => Yii::t('cati', 'Versión PAIM'),
            'value' => function ($respuesta) { return $respuesta->versionPaim ?? null; },
        ],
    ],
    'options' => ['class' => 'cabecera-azul'],
    'summary' => '', // Do not show `Showing 1-19 of 19 items'.
    'tableOptions' => ['class' => 'table table-striped table-hover', 'id' => 'tabla_acciones'],
    ]
);

echo "</div>\n";  // table-responsive
echo "<br>\n";
?>


<!-- DataTables -->
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script>
<script>
    // https://datatables.net/reference/option/
    document.addEventListener("DOMContentLoaded", () => {
        $.noConflict();  // Avoid Uncaught TypeError: $(...).DataTable is not a function

        $('#tabla_acciones').DataTable({
            "buttons": [{ // https://datatables.net/extensions/buttons/
                className: 'btn btn-info',
                extend: 'csv',
                filename: "acciones-<?= $anyo ?>-<?= $pregunta->apartado ?>",
                text: 'Descargar',
            }],
            "dom": 'Bfrtip', // https://datatables.net/reference/option/dom
            "info": false,
            // "language":
            'order': [[3, 'asc'], [4, 'asc'], [5, 'asc']],
            "ordering": true,
            "paging": false,
            "searching": false,
        });

    });
</script>