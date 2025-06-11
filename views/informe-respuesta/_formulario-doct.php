<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use marqu3s\summernote\Summernote;

if (!$pregunta->editable or ($estudio->anyos_evaluacion == 1 && !$pregunta->editable_1)) {
    return;
}
require_once Yii::getAlias('@app') . '/views/informe/doct/_descripciones.php';

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

if ($pregunta->tabla) {
    $tablas = explode(",", $pregunta->tabla);
    foreach ($tablas as $tabla) {
        echo $this->render(
            '@app/views/informe/doct/_' . $tabla,
            [
                'anyo' => $anyo,
                'datos' => $datos,
                'descripciones' => $descripciones,
                'encuestas' => $encuestas,
                'estudio' => $estudio,
                'estudio_id_nk' => $estudio->id_nk,
                'ultimos_datos' => $ultimos_datos,
            ]
        );
    }
}

if ($pregunta->explicacion) {
    echo "<div class='alert alert-info'>\n";
    echo "  <div class='glyphicon glyphicon-info-sign'></div>\n";
    echo '  <div>' . HtmlPurifier::process($pregunta->explicacion) . "</div>\n";
    if ($pregunta->info) {
        echo "  <div><a href='" . HtmlPurifier::process($pregunta->info) . "'>Más información</a></div>\n";
    }
    echo "</div>\n";
}

if ($pregunta->texto_comun) {
    echo '  <div>' . HtmlPurifier::process($pregunta->texto_comun) . "</div><br />\n\n";
}

$form = ActiveForm::begin([
    // 'action' => ...,
    'id' => 'informe-pregunta',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-error',
]);

$texto = '';
if ($respuesta and $respuesta->contenido) {
    $texto = HtmlPurifier::process(
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
                'rules', 'size', 'start', 'valign', 'vspace', 'width', ],
            'HTML.ForbiddenElements' => ['applet', 'font', 'html', 'noframes', 'noscript', 'script', 'style', ],
            // 'data' permite incrustar imágenes en base64
            'URI.AllowedSchemes' => ['data' => true, 'http' => true, 'https' => true, 'mailto' => true],
        ]
    );
    $texto = str_replace('<table>', "<table class='table table-bordered'>", $texto);
    $texto = (substr($texto, -6) === '<br />') ? $texto : $texto . '<br />';
}

echo Summernote::widget([
    'id' => 'contenido',
    'name' => 'contenido',
    'value' => $texto,
    'clientOptions' => [
        'lang' => Yii::$app->catilanguage->getLocale(Yii::$app->language),
        'placeholder' => Yii::t('cati', 'Introduzca sus comentarios'),
    ],
]) . "\n\n";

?>

<hr>

<?php echo Html::submitButton(
    '<span class="glyphicon glyphicon-check"></span> ' . Yii::t('cruds', 'Save'),
    [
        'id' => 'save-respuesta',
        'class' => 'btn btn-success',
    ]
);
?>

<?php ActiveForm::end(); ?>
