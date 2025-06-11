<?php
/**
 * Vista de la lista de las acciones de los planes de innovación y mejora de un centro.
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

$this->title = $centro->nombre;
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Acciones'),
    'url' => ['gestion/seleccionar-centro-paim', 'anyo' => $anyo],
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
/*
function mostrarAtributo($pregunta, $campo)
{
    return false !== strpos($pregunta->atributos, $campo);
}
*/
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
foreach ($preguntas as $pregunta) {
    $respuestas_pregunta = array_filter($respuestas, function ($r) use ($pregunta) {
        return $r->plan_pregunta_id === $pregunta->id;
    });
    if (!$respuestas_pregunta) {
        continue;
    }
    echo "<h2>{$pregunta->apartado}.— {$pregunta->titulo}</h2>\n";
    if ($pregunta->explicacion) {
        echo "<p class='alert alert-info'>\n";
        echo "<span class='glyphicon glyphicon-info-sign'></span> " . HtmlPurifier::process($pregunta->explicacion) . "\n";
        echo "</p>\n";
    }
    /*
    // In the GridView 'visible' cannot be set to a callable.
    $mostrarTitulo = mostrarAtributo($pregunta, 'titulo');
    $mostrarDescripcionBreve = mostrarAtributo($pregunta, 'descripcion_breve');
    $mostrarDescripcionAmplia = mostrarAtributo($pregunta, 'descripcion_amplia');
    $mostrarResponsableAccion = mostrarAtributo($pregunta, 'responsable_accion');
    $mostrarInicio = mostrarAtributo($pregunta, 'inicio');
    $mostrarFinal = mostrarAtributo($pregunta, 'final');
    $mostrarResponsableCompetente = mostrarAtributo($pregunta, 'responsable_competente');
    $mostrarJustificacion = mostrarAtributo($pregunta, 'justificacion');
    $mostrarNivel = mostrarAtributo($pregunta, 'nivel');
    $mostrarFecha = mostrarAtributo($pregunta, 'fecha');
    // Específicos doctorado
    $mostrarProblema = mostrarAtributo($pregunta, 'problema');
    $mostrarObjetivo = mostrarAtributo($pregunta, 'objetivo');
    $mostrarAcciones = mostrarAtributo($pregunta, 'acciones');
    $mostrarApartadoMemoria = mostrarAtributo($pregunta, 'apartado_memoria');
    */
    $campos_pregunta = array_map(
        function ($a) { return trim($a); },
        explode(',', $pregunta->atributos)
    );

    $dataProvider = new ArrayDataProvider([
        'allModels' => $respuestas_pregunta,
        'pagination' => false,  // ['pageSize' => 20],
        /*
        'sort' => [
            'attributes' => ['ambito_id', 'necesidad_detectada', 'objetivo'],
            'defaultOrder' => [
                'ambito_id' => SORT_ASC,
                'necesidad_detectada' => SORT_ASC,
                'objetivo' => SORT_ASC
            ]
        ],
        */
    ]);

    echo "<div class='table-responsive'>";

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'estudio.nombre',
                'label' => 'Estudio',
            ], [
                'attribute' => 'id',
                // En la sección de «Fecha aprobación CGC» no mostramos el ID.
                'visible' => ($pregunta->atributos != 'fecha'),
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
            ],[
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
                'visible' => in_array('valores_a_alcanzar', $campos_pregunta),
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
        'tableOptions' => ['class' => 'table table-striped table-hover', 'id' => "tabla-{$pregunta->id}"],
    ]);
    echo "</div>\n";  // table-responsive
    echo "<br>\n";
}

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
        <?php foreach ($preguntas as $pregunta) { ?>
        $('#tabla-<?= $pregunta->id ?>').DataTable({
            "buttons": [{ // https://datatables.net/extensions/buttons/
                className: 'btn btn-info',
                extend: 'csv',
                filename: "acciones-<?= $anyo ?>-<?= $centro->id ?>-<?= $pregunta->apartado ?>",
                text: 'Descargar',
            }],
            "dom": 'Bfrtip', // https://datatables.net/reference/option/dom
            "info": false,
            // "language":
            'order': [[2, 'asc'], [3, 'asc'], [4, 'asc']],
            "ordering": true,
            "paging": false,
            "searching": false,
        });
        <?php } ?>
    });
</script>