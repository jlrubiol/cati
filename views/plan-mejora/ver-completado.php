<?php
/**
 * Vista de un plan de innovación y mejora.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use app\models\PlanPublicado;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
?>

<!-- DataTables -->
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script>

<?php
$this->title = Yii::t('cati', 'Plan anual de innovación y mejora') . ' — '
    . Html::encode($estudio->nombre);
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => [$estudio->getMetodoVerEstudio(), 'id' => $estudio->id_nk],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Plan de mejora');

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
@page {
  size: a3 landscape;
}
#contenedor-principal {
    width: 1440px;
}
CSS;

$this->registerCss($css);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<h2><?php echo Yii::t('cati', 'Curso') . ' ' . $anyo . '/' . ($anyo + 1); ?></h2>
<hr><br>

<?php
foreach ($preguntas as $i => $pregunta) {
    $i++;  // Para que los nombres de los ficheros empiecen a contar por 1 y no por 0.
    // 1 => <h2>, 1.1 => <h3>, 1.1.1 => <h4>
    $level = count(explode('.', $pregunta->apartado)) + 1;
    $start = '<h' . $level . '>';
    $end = '</h' . $level . '>';


    $campos_pregunta = array_map(function ($a) { return trim($a); }, explode(',', $pregunta->atributos));

    echo "$start" . HtmlPurifier::process($pregunta->apartado) . '.— '
      . HtmlPurifier::process($pregunta->titulo) . "$end\n";

    if (!isset($respuestas[$pregunta->id])) {
        continue;
    }
    $dataProvider = new ArrayDataProvider(
        [
            'allModels' => $respuestas[$pregunta->id],
            'pagination' => false,  // ['pageSize' => 10],
            # 'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_ASC]],
            'sort' => [
                'attributes' => ['ambito_id', 'necesidad_detectada', 'objetivo'],
                'defaultOrder' => ['ambito_id' => SORT_ASC, 'necesidad_detectada' => SORT_ASC, 'objetivo' => SORT_ASC]
            ],
        ]
    );

    echo "<div class='table-responsive'>\n";
    echo GridView::widget(
        [
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'id',
                'visible' => ($pregunta->atributos != 'fecha'),  // En la sección de «Fecha aprobación CGC» no mostramos el ID.
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
                'headerOptions' => ['class' => 'col-sm-2'],
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
                'visible' => in_array('valores_a_alcanzar', $campos_pregunta),
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
                'visible' => ($pregunta->atributos != 'fecha'),  // En la sección de «Fecha aprobación CGC» no mostramos el estado.
                'value' => function ($respuesta) { return $respuesta->estado['valor'] ?? null; },
            ],
        ],
        'options' => ['class' => 'cabecera-azul'],
        'summary' => '',  // Do not show `Showing 1-19 of 19 items'.
        'tableOptions' => [
            'class' => 'table table-striped table-hover',
            'id' => "tabla_paim_{$i}",
        ],
        ]
    );
    echo "</div>\n";  # table-responsive
}  # foreach preguntas
?>

<br>
<script>
// https://datatables.net/reference/option/
document.addEventListener("DOMContentLoaded", () => {
    $.noConflict();  // Avoid Uncaught TypeError: $(...).DataTable is not a function

    <?php for ($i=1; $i < 3; $i++) { ?>
        $("#tabla_paim_<?= $i ?>").DataTable({
            "buttons": [  // https://datatables.net/extensions/buttons/
                {
                    className: 'btn btn-info noprint',
                    extend: 'csv',
                    filename: `acciones_paim_<?= $i ?>`,
                    text: '⬇ Descargar como CSV',
                }, {
                    text: '🛈 Cómo abrir un CSV con MS Excel',
                    action: function (e, dt, button, config) {
                        window.open("<?php Yii::getAlias('@web') ?>" + "/pdf/como_abrir_un_csv_con_excel.pdf");
                    }
                }
            ],
            "dom": 'Bfrtip', // https://datatables.net/reference/option/dom
            "info": false,
            // "language":
            'order': [[0, 'asc'],],
            "ordering": true,
            "paging": false,
            "searching": false,
        });
    <?php } ?>
});
</script>

<?php
echo "<div class='noprint'>";
echo Html::a(
    '<span class="glyphicon glyphicon-eye-open"></span> ' . // Button
    Yii::t('cati', 'Previsualizar PDF'),
    ['previsualizar', 'estudio_id' => $estudio->id, 'anyo' => $anyo, 'completado' => True],
    [
        'id' => 'ver-pdf',
        'class' => 'btn btn-info',
        'title' => Yii::t('cati', 'Generar un PDF para previsualizar el resultado'),
    ]
) . " &nbsp; \n";
echo '</div>';
