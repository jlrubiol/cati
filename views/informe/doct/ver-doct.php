<?php
/**
 * Vista de un informe de evaluación de doctorado.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use app\assets\ChartJsAsset;

$bundle = ChartJsAsset::register($this);

$this->title = Yii::t(
    'cati',
    'Informe de Evaluación de la Calidad'
) . ' — ' . Html::encode($estudio->nombre);
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver-doct', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Informe de Evaluación de la Calidad');

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);

// Override Bootstrap, which sets color to #000!important;
$css = <<<CSS
@media print {
    h1, h2, h3, h4 {
        color: #000080 !important;
    }
}
CSS;

$this->registerCss($css);

require_once '_descripciones.php';

/**
 * Devuelve el porcentaje que supone un dato sobre un total.
 *
 * @param float $dato  Dato
 * @param float $total Total
 *
 * @return string Porcentaje con un decimal
 */
function percent($dato, $total)
{
    if (0 == $total) {
        return number_format(0.0, 1, '.', '');
    }
    // ISO 80000-1 7.3 : The decimal sign is either a comma or a point on the line.
    // The same decimal sign should be used consistently within a document.
    // A &thinsp; (U+2009) may be used as a thousands separator.
    return number_format(round($dato * 100 / $total, 1), 1, '.', '');
}

?>

<script src="<?php echo $bundle->baseUrl; ?>/Chart.bundle.js"></script>
<?php echo $this->render('../_chart_config'); ?>

<!-- Título del documento y sus periodos de evaluación -->
<h1><?php echo Html::encode($this->title); ?></h1>
<h2><?php
    if ($estudio->anyos_evaluacion == 1) {
        printf(Yii::t('cati', 'Periodo de evaluación: %d año académico'), $estudio->anyos_evaluacion);
    } else {
        printf(Yii::t('cati', 'Periodo de evaluación: %d años académicos'), $estudio->anyos_evaluacion);
    }
?></h2>
<ul>
    <?php
        $primer_anyo = $estudio->anyo_academico - $estudio->anyos_evaluacion + 1;
        for ($a = $primer_anyo; $a <= $estudio->anyo_academico; $a++) {
            printf('<li>' . Yii::t('cati', 'Curso %d/%d') . '</li>', $a, $a + 1);
        }
    ?>
</ul>
<hr><br>

<p class='alert alert-info'>
    <span class='glyphicon glyphicon-info-sign'></span>
    <?php
    echo Yii::t(
        'doct',
        'En este enlace está disponible la <strong><a href="https://inspecciongeneral.unizar.es/sites/inspecciongeneral/files/archivos/calidad_mejora/A-Q212_2.pdf" target="_blank">descripción de los indicadores</a></strong> <span class="glyphicon glyphicon-link" style="float:none; font-size: inherit;"></span>.<br>
        Si tiene alguna duda sobre la información que se muestra en las tablas de datos,
        envíe su consulta a uzcalidad@unizar.es, donde, una vez estudiada,
        la trasladará al responsable que corresponda para su resolución.'
    );
    ?>
</p>

<?php
# Índice de apartados de primer nivel: cuadro gris no imprimible
$preguntas_n1 = array_filter($preguntas, function ($p) {
    return (strpos($p->apartado, '.') === false);
});

echo "<div class='cuadro-gris hidden-print'>\n";
printf("<h3>%s</h3>\n", Yii::t('cati', 'Índice'));
foreach ($preguntas_n1 as $pregunta) {
    printf(
        "%s. <a href='#%d'>%s</a><br>\n",
        HtmlPurifier::process($pregunta->apartado),
        $pregunta->id,
        HtmlPurifier::process($pregunta->titulo)
    );
}
echo "</div>\n";

# Iteramos por las preguntas del informe
foreach ($preguntas as $pregunta) {
    if ($estudio->anyos_evaluacion == 1 && $pregunta->invisible_1) continue;
    if ($estudio->anyos_evaluacion == 3 && $pregunta->invisible_3) continue;

    # Encabezado con el título de la pregunta
    echo "<a id='{$pregunta->id}'></a>";
    // 1 => <h2>, 1.1 => <h3>, 1.1.1 => <h4>
    $level = count(explode('.', $pregunta->apartado)) + 1;
    $start = "<h{$level}>";
    $end = "</h{$level}>\n";
    printf(
        "\n%s%s.— %s%s\n",
        $start,
        HtmlPurifier::process($pregunta->apartado),
        HtmlPurifier::process($pregunta->titulo),
        $end
    );

    # Tabla(s) de la pregunta, si la(s) hay
    if ($pregunta->tabla) {
        $tablas = explode(",", $pregunta->tabla);
        foreach ($tablas as $num_tabla => $tabla) {
            echo $this->render(
                '_' . $tabla,
                [
                    'anyo' => $anyo,
                    'apartado' => $pregunta->apartado,
                    'estudio' => $estudio,
                    'datos' => $datos,
                    'descripciones' => $descripciones,
                    'estudio_anterior' => $estudio_anterior,
                    'encuestas' => $encuestas,
                    'num_tabla' => $num_tabla,
                    'preguntas_paim' => $preguntas_paim,
                    'respuestas_paim' => $respuestas_paim,
                    'titulo' => $pregunta->titulo,
                    'ultimos_datos' => $ultimos_datos,
                ]
            );
        }
    }

    # Texto común introducido por la Unidad, si lo hay
    if ($pregunta->texto_comun) {
        echo '  <div>' . HtmlPurifier::process(nl2br($pregunta->texto_comun)) . "</div><br />\n\n";
    }

    # Respuesta del coordinador a la pregunta
    $respuesta = yii\helpers\ArrayHelper::getValue($respuestas, $pregunta->id, null);
    if ($respuesta) {
        echo '<div>' . HtmlPurifier::process(
            $respuesta->contenido,
            [
                'Attr.ForbiddenClasses' => ['Apple-interchange-newline', 'Apple-converted-space',
                    'Apple-paste-as-quotation', 'Apple-style-span', 'Apple-tab-span', 'BalloonTextChar', 'BodyTextIndentChar',
                    'Heading1Char', 'Heading2Char', 'Heading3Char', 'Heading4Char', 'Heading5Char', 'Heading6Char',
                    'IntenseQuoteChar', 'MsoAcetate', 'MsoBodyText', 'MsoBodyText1', 'MsoBodyText2', 'MsoBodyText3',
                    'MsoBodyTextIndent', 'MsoBookTitle', 'MsoCaption', 'MsoChpDefault',
                    'MsoFooter', 'MsoHeader', 'MsoHyperlink', 'MsoHyperlinkFollowed',
                    'MsoIntenseEmphasis', 'MsoIntenseQuote', 'MsoIntenseReference', 'MsoListParagraph',
                    'MsoListParagraphCxSpFirst', 'MsoListParagraphCxSpMiddle', 'MsoListParagraphCxSpLast',
                    'MsoNormal', 'MsoNormalTable', 'MsoNoSpacing', 'MsoPapDefault', 'MsoQuote',
                    'MsoSubtleEmphasis', 'MsoSubtleReference', 'MsoTableGrid',
                    'MsoTitle', 'MsoTitleCxSpFirst', 'MsoTitleCxSpMiddle', 'MsoTitleCxSpLast',
                    'MsoToc1', 'MsoToc2', 'MsoToc3', 'MsoToc4', 'MsoToc5', 'MsoToc6', 'MsoToc7', 'MsoToc8', 'MsoToc9',
                    'MsoTocHeading', 'QuoteChar', 'SubtitleChar', 'TitleChar',
                    'western', 'WordSection1', ],
                'CSS.ForbiddenProperties' => ['background', 'border', 'border-bottom',
                    'border-collapse', 'border-left', 'border-right', 'border-style', 'border-top', 'border-width',
                    'font', 'font-family', 'font-size', 'font-weight', 'height', 'line-height',
                    'margin', 'margin-bottom', 'margin-left', 'margin-right', 'margin-top',
                    'padding', 'padding-bottom', 'padding-left', 'padding-right', 'padding-top',
                    'text-autospace', 'text-indent', 'width', ],
                'HTML.ForbiddenAttributes' => ['align', 'background', 'bgcolor', 'border',
                    'cellpadding', 'cellspacing', 'height', 'hspace', 'noshade', 'nowrap',
                    'rules', 'size', 'valign', 'vspace', 'width', ],
                'HTML.ForbiddenElements' => ['font'],
                // 'data' permite incrustar imágenes en base64
                'URI.AllowedSchemes' => ['data' => true, 'http' => true, 'https' => true, 'mailto' => true],
            ]
        ) . "</div>\n\n";
    }

    # Botones para editar la respuesta
    if ($mostrar_botones && $pregunta->editable && !($estudio->anyos_evaluacion == 1 && !$pregunta->editable_1)) {
        // Usamos una hoja de estilo para no mostrar los botones al imprimir a PDF
        echo "<div class='noprint'>\n";

        if ($pregunta->explicacion) {
            echo "<div class='alert alert-info'>\n";
            echo "  <span class='glyphicon glyphicon-info-sign'></span>\n";
            echo '  <div>' . HtmlPurifier::process($pregunta->explicacion) . "</div>\n";
            if ($pregunta->info) {
                echo "  <div><a href='" . HtmlPurifier::process($pregunta->info) . "'>Más información</a></div>\n";
            }
            echo "</div>\n\n";
        }

        if (Yii::$app->language === 'en') {
            ?>
            <div class='alert alert-warning'>
                <span class='glyphicon glyphicon-exclamation-sign'></span>
                Está rellenando el informe en inglés. Casi con total seguridad debería hacerlo en castellano.
            </div>
            <?php
        }

        if ($respuesta) {
            echo Html::a(
                '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('cati', 'Editar comentarios'),
                ['informe-respuesta/editar-doct', 'estudio_id' => $estudio->id, 'informe_respuesta_id' => $respuesta->id],
                ['id' => "editar-respuesta-{$pregunta->id}", 'class' => 'btn btn-success']
            ) . "\n";
        } else {
            echo Html::a(
                '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('cati', 'Añadir comentarios'),
                ['informe-respuesta/crear-doct', 'estudio_id' => $estudio->id, 'informe_pregunta_id' => $pregunta->id],
                ['id' => "crear-respuesta-{$pregunta->id}", 'class' => 'btn btn-success']
            ) . "\n";
        }
        echo "</div><br>\n\n";
    }
}

####################################################################

echo "<br>\n";

# Botones para previsualizar el PDF o cerrar la versión del informe.
if ($mostrar_botones) {
    // Usamos una hoja de estilo para no mostrar los botones al imprimir a PDF
    echo "<div class='noprint'>";
    echo Html::a(
        '<span class="glyphicon glyphicon-eye-open"></span> ' . // Button
        Yii::t('cati', 'Previsualizar PDF IEC'),
        ['previsualizar', 'estudio_id' => $estudio->id, 'anyo' => $anyo],
        [
            'id' => 'ver-pdf',
            'class' => 'btn btn-info',
            'title' => Yii::t('cati', 'Generar un PDF para previsualizar el resultado'),
        ]
    ) . " &nbsp; \n";

    if ($estudio_anterior) {
        echo Html::a(
            '<span class="glyphicon glyphicon-eye-open"></span> ' . // Button
            Yii::t('cati', 'Previsualizar PDF PAIM'),
            ['plan-mejora/previsualizar', 'estudio_id' => $estudio_anterior->id, 'anyo' => $estudio_anterior->anyo_academico, 'completado' => True],
            [
                'id' => 'ver-pdf',
                'class' => 'btn btn-info',
                'title' => Yii::t('cati', 'Generar un PDF para previsualizar el PAIM completado'),
            ]
        ) . " &nbsp; \n";
    }

    echo Html::a(
        '<span class="glyphicon glyphicon-check"></span> ' .
          Yii::t('cati', 'Finalizar informe'),
        ['', '#' => 'modalCerrarInforme'],
        [
            'id' => 'cerrar-informe',
            'class' => 'btn btn-danger',
            'data-toggle' => 'modal',
            'title' => Yii::t(
                'cati',
                'Finalizar el informe.  Genera el PDF y se envía por correo a los agentes correspondientes.'
            ),
        ]
    );
    echo '</div>';
}

/*
echo $this->render(
    '_anexo',
    ['descripciones' => $descripciones]
);
*/

echo '<h2>Anexo</h2>';
echo Html::a(
    Yii::t('cati', 'Catálogo de indicadores del IEC y del ICED'),
    'https://inspecciongeneral.unizar.es/sites/inspecciongeneral/files/archivos/calidad_mejora/A-Q212_2.pdf'
) . " <span class='glyphicon glyphicon-link'></span>";
?>

<!-- Diálogo modal -->
<div id="modalCerrarInforme" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo Yii::t('gestion', '¿Finalizar el informe?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?php echo Yii::t(
                    'gestion',
                    '¿Seguro que desea dar por finalizada este Informe de evaluación?'
                ); ?></p>
                <p><?php echo Yii::t(
                    'gestion',
                    'Las respuestas que ha introducido hasta ahora ya están guardadas,'
                    . ' y puede seguir añadiendo más, ahora o en otro momento.'
                    . ' Pero si cierra la versión, se enviará por correo electrónico'
                    . ' a las personas correspondientes y <b>ya no podrá seguir editándola</b>.'
                ); ?></p>
                <p><?php echo Yii::t(
                    'gestion',
                    'Si simplemente desea una vista previa del PDF resultante,'
                        . ' vuelva atrás y pulse el botón <b>Previsualizar PDF</b>.'
                ); ?></p>
            </div>
            <div class="modal-footer">
                <?php echo Html::a(
                    Yii::t('gestion', 'Finalizar informe'),
                    ['cerrar', 'estudio_id' => $estudio->id, 'anyo' => $anyo],
                    [
                        'id' => 'cerrar',
                        'class' => 'btn btn-danger',  // Button
                        'title' => Yii::t('gestion', 'Finalizar informe'),
                    ]
                ); ?>

                <button type="button" class="btn btn-info" data-dismiss="modal">
                    <?php echo '<span class="glyphicon glyphicon-remove"></span> ' . Yii::t('gestion', 'Cancelar'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
