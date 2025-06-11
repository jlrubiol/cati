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
$siguiente_anyo = $anyo + 1;
$siguesigue_anyo = $siguiente_anyo + 1;

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

$version_maxima = PlanPublicado::MAX_VERSION_PLAN;
if ($estudio->esDoctorado()) {
    $version_maxima = PlanPublicado::MAX_VERSION_PLAN_DOCT;
    # En el curso 2021-22, PlanPublicado::MAX_VERSION_PLAN_DOCT pasó de ser 1 a 2.
    if ($estudio->anyo_academico < 2021) {
        $version_maxima -= 1;
    }
}

// Yii::$app->user es de clase `yii\web\User`, $usuario es de clase `app\models\User`
$usuario = Yii::$app->user->identity;
// En los PD, tanto el presidente como la secretaria de la Comisión de Doctorado
// pueden acceder a la V1 de los PAIM (que ha cerrado previamente el coordinador)
// y hacer modificaciones (pero no cerrarla).
$mostrarBotones = ($nueva_version <= $version_maxima
  and (Yii::$app->user->can('editarPlan', ['estudio' => $estudio]) or ($estudio->esDoctorado() and $usuario and $usuario->esComisionDoctorado())));

if ($mostrarBotones) {
    $usuario = Yii::$app->user->identity;
    $presidentes = $estudio->getNipPresidentesGarantiaYDelegados();
    $esPresidente = in_array($usuario->username, $presidentes);
    $coorDeles = $estudio->getNipCoordinadoresYDelegados();
    $esCoorDele = in_array($usuario->username, $coorDeles);

    if (1 == $nueva_version) {
        // La primera versión la cumplimenta y cierra el coordinador.
        $mostrarBotones = ($esCoorDele or Yii::$app->user->can('editarPlan'));
    } else {
        // La 2a y 3a versión la revisa y cierra el presidente de la comisión de garantía de la calidad
        $mostrarBotones = ($esPresidente or Yii::$app->user->can('editarPlan') or ($estudio->esDoctorado() and $usuario and $usuario->esComisionDoctorado()));
    }
}
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<h2><?php echo Yii::t('cati', "Para el curso {$siguiente_anyo}/{$siguesigue_anyo}"); ?></h2>
<hr><br>

<?php
foreach ($preguntas as $i => $pregunta) {
    $i++;  // Para que los nombres de los ficheros empiecen a contar por 1 y no por 0.
    // 1 => <h2>, 1.1 => <h3>, 1.1.1 => <h4>
    $level = count(explode('.', $pregunta->apartado)) + 1;
    $start = '<h' . $level . '>';
    $end = '</h' . $level . '>';


    $campos_pregunta = array_map(function ($a) { return trim($a); }, explode(',', $pregunta->atributos));

    /*
    // In the GridView 'visible' cannot be set to a callable.
    $mostrarId = ($pregunta->atributos != 'fecha');  // En la sección de Fecha aprobación no mostramos el ID.
    $mostrarApartadoMemoria = false !== strpos($pregunta->atributos, 'apartado_memoria');
    $mostrarTitulo = false !== strpos($pregunta->atributos, 'titulo');
    $mostrarDescripcionBreve
        = false !== strpos($pregunta->atributos, 'descripcion_breve');
    $mostrarDescripcionAmplia
        = false !== strpos($pregunta->atributos, 'descripcion_amplia');
    $mostrarResponsableAccion
        = false !== strpos($pregunta->atributos, 'responsable_accion');
    $mostrarInicio = false !== strpos($pregunta->atributos, 'inicio');
    $mostrarFinal = false !== strpos($pregunta->atributos, 'final');
    $mostrarResponsableCompetente
        = false !== strpos($pregunta->atributos, 'responsable_competente');
    $mostrarJustificacion = false !== strpos($pregunta->atributos, 'justificacion');
    $mostrarNivel = false !== strpos($pregunta->atributos, 'nivel');
    $mostrarFecha = false !== strpos($pregunta->atributos, 'fecha');
    $mostrarProblema = false !== strpos($pregunta->atributos, 'problema');
    $mostrarObjetivo = false !== strpos($pregunta->atributos, 'objetivo');
    $mostrarAcciones = false !== strpos($pregunta->atributos, 'acciones');
    $mostrarPlazoImplantacion = false !== strpos($pregunta->atributos, 'plazo_implantacion');
    $mostrarIndicador = false !== strpos($pregunta->atributos, 'indicador');
    $mostrarMeta = false !== strpos($pregunta->atributos, 'meta');
    $mostrarValor = false !== strpos($pregunta->atributos, 'valor');
    $mostrarCumplimiento = false !== strpos($pregunta->atributos, 'cumplimiento');
    */

    echo "$start" . HtmlPurifier::process($pregunta->apartado) . '.— '
      . HtmlPurifier::process($pregunta->titulo) . "$end\n";

    if ($mostrarBotones) {
        // Usamos una hoja de estilo para no mostrar los botones al imprimir a PDF
        echo "<div class='noprint'>\n";

        if ($pregunta->explicacion) {
            echo "<div class='alert alert-info'>\n";
            echo "  <span class='glyphicon glyphicon-info-sign'></span>\n";
            echo '  <div>' . HtmlPurifier::process($pregunta->explicacion) . "</div>\n";
            echo "</div>\n";
        }

        if (Yii::$app->language === 'en') {
            ?>
            <div class='alert alert-warning'>
                <span class='glyphicon glyphicon-exclamation-sign'></span>
                Está rellenando el plan en inglés. Casi con total seguridad debería hacerlo en castellano.
            </div>
            <?php
        }

        echo Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' .
            Yii::t('cati', 'Añadir registro'),
            [
                'crear',
                'estudio_id' => $estudio->id,
                // 'anyo' => $anyo,
                'plan_pregunta_id' => $pregunta->id,
            ],
            [
                'id' => "editar-plan-{$i}",
                'class' => 'btn btn-success', // Button
            ]
        ) . "\n";
        echo "</div><br>\n";

        # No mostramos el formulario de importar acción en el últimpo apartado (Fecha de aprobación)
        if ($pregunta->atributos != 'fecha') {
            echo Html::beginForm(Url::to(['/plan-mejora/importar-accion']), 'post');
            # echo Html::input('hidden', Yii::$app->request->csrfParam, Yii::$app->request->csrfToken);
            echo Html::input('hidden', 'estudio_id', $estudio->id);
            echo Html::input('hidden', 'plan_pregunta_id', $pregunta->id);
            echo Html::input('hidden', 'apartado_id', $pregunta->apartado);
            echo Html::beginTag('div', ['class' => 'col-sm-2']);
            echo Html::input('number', 'accion_id', null, ['placeholder'=> 'ID de la acción', 'class' => 'form-control']);
            echo Html::endTag('div');
            echo Html::submitButton('<span class="glyphicon glyphicon-plus"></span> ' . 'Importar acción del anterior PAIM', ['class' => 'btn btn-success']);
            echo Html::endForm() . "<br>\n";
        }
    }

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
                'visible' => in_array('valores_alcanzados', $campos_pregunta),
            ], [
                'attribute' => 'justificacion_breve',
                'visible' => in_array('justificacion_breve', $campos_pregunta),
            ], [
                'attribute' => 'observaciones',
                'visible' => in_array('observaciones', $campos_pregunta),
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
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'editar' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('cati', 'Editar el registro'),
                            'aria-label' => Yii::t('cati', 'Editar el registro'),
                            'data-pjax' => '0',
                            'class' => 'text-info',
                        ];

                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            $url,
                            $options
                        );
                    },
                    'borrar' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('cati', 'Borrar el registro'),
                            'aria-label' => Yii::t('cati', 'Borrar el registro'),
                            'data-confirm' => Yii::t('gestion', '¿Seguro que desea eliminar este registro?'),
                            'data-params' => ['estudio_id' => $model->estudio_id],
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'class' => 'text-danger',
                        ];

                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            $url,
                            $options
                        );
                    },
                    'add-accion' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('cati', 'Añadir una acción con el mismo objetivo'),
                            'aria-label' => Yii::t('cati', 'Añadir una acción con el mismo objetivo'),
                            'data-pjax' => '0',
                            'class' => 'text-success',
                        ];

                        return Html::a(
                            '<span class="glyphicon glyphicon-plus"></span>',
                            $url,
                            $options
                        );
                    },
                ],
                // 'controller' => '',
                'template' => '{editar} {borrar} {add-accion}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    $params = [
                        $action,
                        'estudio_id' => $model->estudio_id,
                        'id' => $model->id,
                    ];

                    return Url::toRoute($params);
                },
                // visibleButtons => ...,
                'contentOptions' => ['nowrap' => 'nowrap'],
                'visible' => $mostrarBotones,
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

if ($mostrarBotones) {
    // Usamos una hoja de estilo para no mostrar los botones al imprimir a PDF
    echo "<div class='noprint'>";
    echo Html::a(
        '<span class="glyphicon glyphicon-eye-open"></span> ' . // Button
        Yii::t('cati', 'Previsualizar PDF'),
        ['previsualizar', 'estudio_id' => $estudio->id, 'anyo' => $anyo],
        [
            'id' => 'ver-pdf',
            'class' => 'btn btn-info',
            'title' => Yii::t('cati', 'Generar un PDF para previsualizar el resultado'),
        ]
    ) . " &nbsp; \n";

    echo Html::a(
        '<span class="glyphicon glyphicon-check"></span> '
          . Yii::t('cati', "Cerrar versión $nombre_nueva_version"),
        ['', '#' => 'modalCerrarPlan'],
        [
            'id' => 'cerrar-plan',
            'class' => 'btn btn-danger',
            'data-toggle' => 'modal',
            'title' => Yii::t(
                'cati',
                'Finalizar el plan.  Genera el PDF y se envía por correo a los agentes correspondientes.'
            ),
        ]
    );
    echo '</div>';
}

?>
<!-- Diálogo modal -->
<div id="modalCerrarPlan" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo Yii::t('gestion', '¿Cerrar versión?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?php echo Yii::t(
                    'gestion',
                    '¿Seguro que desea dar por finalizada esta versión del Plan de innovación y mejora?'
                ); ?></p>
                <p><?php echo Yii::t(
                    'gestion',
                    'Los registros que ha añadido hasta ahora ya están guardados,'
                    . ' y puede seguir añadiendo más, ahora o en otro momento.'
                    . ' Pero si cierra la versión, se generará el PDF, se enviará por correo electrónico'
                    . ' a las personas correspondientes, y <b>ya no podrá seguir editándola</b>.'
                ); ?></p>
                <p><?php echo Yii::t(
                    'gestion',
                    'Si simplemente desea una vista previa del PDF resultante,'
                        . ' vuelva atrás y pulse el botón <b>Previsualizar PDF</b>.'
                ); ?></p>
            </div>
            <div class="modal-footer">
                <?php echo Html::a(
                    Yii::t('gestion', 'Cerrar versión'),
                    ['cerrar', 'estudio_id' => $estudio->id, 'anyo' => $anyo],
                    [
                        'id' => 'cerrar',
                        'class' => 'btn btn-danger',  // Button
                        'title' => Yii::t('gestion', 'Dar por finalizada esta versión'),
                    ]
                ); ?>

                <button type="button" class="btn btn-info" data-dismiss="modal">
                    <?php echo '<span class="glyphicon glyphicon-remove"></span> ' . Yii::t('gestion', 'Cancelar'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
