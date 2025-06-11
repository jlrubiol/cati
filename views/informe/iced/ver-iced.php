<?php
/**
 * Vista del informe de la calidad de los estudios de doctorado.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use app\assets\ChartJsAsset;
use app\models\InformePublicado;

$bundle = ChartJsAsset::register($this);

$this->title = Yii::t('cati', 'Informe de la calidad de los estudios de doctorado');
$this->params['breadcrumbs'][] = Yii::t('cati', 'Informe de la calidad de los estudios de doctorado');

// Cambiar color de fondo
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);

require_once '_descripciones.php';


?>

<script src="<?php echo $bundle->baseUrl; ?>/Chart.bundle.js"></script>
<?php echo $this->render('../_chart_config'); ?>

<h1><?php echo Html::encode($this->title); ?></h1>
<h2><?php echo Yii::t('cati', 'Curso') . ' ' . $anyo . '/' . ($anyo + 1); ?></h2>
<hr><br>

<p class='alert alert-info'>
    <span class='glyphicon glyphicon-info-sign'></span>
    <?php
    echo Yii::t(
        'doct',
        'En este enlace está disponible la <strong><a href="https://inspecciongeneral.unizar.es/sites/inspecciongeneral/files/archivos/calidad_mejora/A-Q212_2.pdf" target="_blank">descripción de los indicadores</a></strong> <span class="glyphicon glyphicon-link" style="float:none; font-size: inherit; margin-right: 0px;"></span>.<br>
        Si tiene alguna duda sobre la información que se muestra en las tablas de datos,
        envíe su consulta a uzcalidad@unizar.es, donde, una vez estudiada,
        la trasladará al responsable que corresponda para su resolución.'
    );
    ?>
</p>

<?php

echo $this->render(
    '_num_programas',
    [
        'anyo' => $anyo,
        'datos' => $datos,
        'descripciones' => $descripciones,
    ]
);

foreach ($preguntas as $pregunta) {
        echo "<a id='{$pregunta->id}'></a>";

        // 1 => <h2>, 1.1 => <h3>, 1.1.1 => <h4>
        $level = count(explode('.', $pregunta->apartado)) + 1;
        $start = "<h{$level}>";
        $end = "</h{$level}>";

        printf(
            "%s%s.— %s%s\n",
            $start,
            HtmlPurifier::process($pregunta->apartado),
            HtmlPurifier::process($pregunta->titulo),
            $end
        );

    if ($pregunta->tabla) {
        $tablas = explode(",", $pregunta->tabla);
        foreach ($tablas as $tabla) {
            echo $this->render(
                '_' . $tabla,
                [
                'anyo' => $anyo,
                'estudio' => $estudio,
                'datos' => $datos,
                'descripciones' => $descripciones,
                'mostrar_botones' => $mostrar_botones,
                ]
            );
        }
    }

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

    if ($mostrar_botones && $pregunta->editable) {
        // Usamos una hoja de estilo para no mostrar los botones al imprimir a PDF
        echo "<div class='noprint'>\n";

        if ($pregunta->explicacion) {
            echo "<div class='alert alert-info'>\n";
            echo "  <span class='glyphicon glyphicon-info-sign'></span>\n";
            echo '  <div>' . HtmlPurifier::process($pregunta->explicacion) . "</div>\n";
            echo "</div>\n\n";
        }

        if ($respuesta) {
            echo Html::a(
                '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('cati', 'Editar comentarios'),
                ['informe-respuesta/editar-iced', 'informe_respuesta_id' => $respuesta->id],
                ['id' => "editar-respuesta-{$pregunta->id}", 'class' => 'btn btn-success']
            ) . "\n";
        } else {
            echo Html::a(
                '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('cati', 'Añadir comentarios'),
                ['informe-respuesta/crear-iced', 'informe_pregunta_id' => $pregunta->id],
                ['id' => "crear-respuesta-{$pregunta->id}", 'class' => 'btn btn-success']
            ) . "\n";
        }
        echo "</div><br>\n\n";
    }
}

echo "<br>\n";

if ($mostrar_botones) {
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
        '<span class="glyphicon glyphicon-check"></span> ' .
        Yii::t('cati', 'Finalizar informe'),
        ['cerrar', 'estudio_id' => $estudio->id, 'anyo' => $anyo],
        [
            'id' => 'cerrar-informe',
            'class' => 'btn btn-danger',
            'title' => Yii::t(
                'cati',
                'Finalizar el informe.  Genera el PDF y se envía por correo a los agentes correspondientes.'
            ),
        ]
    );
    echo '</div>';
}

// TODO: Botón Publicar informe (en su día)

/*
echo $this->render(
    '_anexo',
    ['descripciones' => $descripciones]
);
*/

echo '<h2>' . Yii::t('doct', 'Anexo') . '</h2>';

echo Html::a(
    Yii::t('cati', 'Catálogo de indicadores del IEC y del ICED'),
    'https://inspecciongeneral.unizar.es/sites/inspecciongeneral.unizar.es/files/archivos/calidad_mejora/A-Q212_2.pdf'
) . " <span class='glyphicon glyphicon-link'></span>";
