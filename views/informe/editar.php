<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use marqu3s\summernote\Summernote;
use app\assets\ChartJsAsset;

$bundle = ChartJsAsset::register($this);

$this->title = Yii::t(
    'cati',
    'Edición del Informe de Evaluación de la Calidad'
) . ' — ' . Html::encode($estudio->nombre);
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('cati', 'Informe de Evaluación de la Calidad'),
    'url' => ['informe/ver', 'estudio_id' => $estudio->id, 'anyo' => $anyo],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Editar');

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);

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
<?php echo $this->render('_chart_config'); ?>

<h1><?php echo Html::encode($this->title); ?></h1>
<h2><?php echo Yii::t('cati', 'Curso') . ' ' . $anyo . '/' . ($anyo + 1); ?></h2>
<hr><br>

<div class="alert alert-warning alert-dismissable fade in">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <div class='glyphicon glyphicon-exclamation-sign'></div>
  <div><?php echo Yii::t('cati', 'Se recomienda ir guardando el informe de vez en'
    . ' cuando, para evitar que se pueda perder la información ya introducida.'); ?><br>
    <!--
    <?php echo Yii::t('cati', 'Si se desea preparar los textos con antelación, es'
    . ' preferible usar un editor de texto sencillo, como el Bloc de notas, en vez de'
    . ' un procesador de texto como Microsoft Word, que tiene sus propios estilos, lo'
    . ' que puede provocar conflictos con el tamaño y tipo de letra.'); ?><br>

    <?php echo Yii::t('cati', 'En caso de observarse este problema, se puede'
    . ' seleccionar el texto del recuadro, y a continuación pulsar el cuarto botón'
    . ' (<span class="note-icon-eraser"></span>, icono de una goma de borrar) para'
    . ' eliminar los estilos.'); ?><br>

    <?php echo Yii::t('cati', 'Si con el botón de borrar estilos no fuera suficiente,'
    . ' la opción más sencilla es cortar el texto del recuadro y pegarlo en el Bloc de'
    . ' notas, y a continuación volver a copiarlo del Bloc de notas y pegarlo'
    . ' en el recuadro.'); ?><br>
    -->
    <?php echo Yii::t('cati', 'Si lo desea, antes de cerrar la versión puede'
    . ' previsualizar el PDF para ver cómo quedará.'); ?></div>
</div>

<?php
$form = ActiveForm::begin([
    'action' => Url::to(['informe/guardar']),
    'id' => 'informe',
    'layout' => 'horizontal',
]);
?>

<input type="hidden" name="estudio_id" value="<?php echo $estudio->id; ?>">
<input type="hidden" name="estudio_id_nk" value="<?php echo $estudio->id_nk; ?>">
<input type="hidden" name="anyo" value="<?php echo $anyo; ?>">

<?php
echo Html::submitButton(
    '<span class="glyphicon glyphicon-check"></span> ' . Yii::t('cruds', 'Save'),
    [
        'id' => 'save-informe',
        'class' => 'btn btn-success',
    ]
);

foreach ($preguntas as $pregunta) :
    $respuesta = array_filter($respuestas, function ($r) use (&$pregunta) {
        return $r->informe_pregunta_id === $pregunta->id;
    });
    $respuesta = current($respuesta);

    // 1 => <h2>, 1.1 => <h3>, 1.1.1 => <h4>
    $level = count(explode('.', $pregunta->apartado)) + 1;
    $start = "<h{$level}>";
    $end = "</h{$level}>\n";

    echo "$start" . HtmlPurifier::process($pregunta->apartado) . '.— ' . HtmlPurifier::process($pregunta->titulo) . "$end\n";
    if ($pregunta->tabla) {
        $tablas = explode(",", $pregunta->tabla);
        foreach ($tablas as $tabla) {
            echo $this->render('_' . $tabla, [
                'anyo' => $anyo,
                'centros' => $centros,
                'dpsCalificaciones' => $dpsCalificaciones,
                'dpsEstudiosPrevios' => $dpsEstudiosPrevios,
                'dpMovilidades' => $dpMovilidades,
                'dpNuevosIngresos' => $dpNuevosIngresos,
                'edades' => $edades,
                'encuestas' => $encuestas,
                'estructuras' => $estructuras,
                'estudio' => $estudio,
                'estudio_id_nk' => $estudio->id_nk,
                'estudiosPrevios' => $estudiosPrevios,
                'evoluciones' => $evoluciones,
                'evolucionesPas' => $evolucionesPas,
                'generos' => $generos,
                'globales' => $globales,
                'indicadores' => $indicadores,
                'indos' => $indos,
                'lista_planes' => $lista_planes,
                'movilidades_in' => $movilidades_in,
                'movilidades_out' => $movilidades_out,
                'movilidad_porcentajes' => $movilidad_porcentajes,
                'notasMedias' => $notasMedias,
                'nuevos_ingresos' => $nuevos_ingresos,
                'planes' => $planes,
                'procedencias' => $procedencias,
                'respuesta' => $respuesta,
            ]);
        }
    }

    if ($pregunta->explicacion) {
        echo "<div class='alert alert-info'>\n";
        echo "  <div class='glyphicon glyphicon-info-sign'></div>\n";
        echo '  <div>' . HtmlPurifier::process($pregunta->explicacion) . "</div>\n";
        echo "</div>\n";
    }

    if ($pregunta->editable) {
        echo Summernote::widget([
            'id' => "$pregunta->id",
            'name' => "$pregunta->id",
            'value' => ($respuesta and $respuesta->contenido) ? HtmlPurifier::process(
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
            ) : '',
            'clientOptions' => [
                'lang' => Yii::$app->catilanguage->getLocale(Yii::$app->language),
                'placeholder' => Yii::t('cati', 'Introduzca sus comentarios'),
            ],
        ]) . "\n\n";
    }
endforeach;  // $preguntas
?>

<hr>

<?php echo Html::submitButton(
    '<span class="glyphicon glyphicon-check"></span> ' . Yii::t('cruds', 'Save'),
    [
        'id' => 'save-informe',
        'class' => 'btn btn-success',
    ]
);
?>

<?php ActiveForm::end(); ?>
