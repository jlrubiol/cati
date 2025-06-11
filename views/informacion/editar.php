<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use marqu3s\summernote\Summernote;

if ('doctorado' == $tipo) {
    $funcion_ver = 'estudio/ver-doct';
} else {
    $funcion_ver = 'estudio/ver';
}

$this->title = Yii::t('cati', 'Edición de la información') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = ['label' => $estudio->nombre, 'url' => [$funcion_ver, 'id' => $estudio->id_nk]];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('cati', 'Editar información'),
    'url' => ['editar-infos', 'estudio_id' => $estudio->id, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = Yii::t('db', $info->seccion->titulo);

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
$locale = Yii::$app->catilanguage->getLocale(Yii::$app->language);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<h2><?php echo Yii::t('db', $info->seccion->titulo); ?></h2>
<hr><br>

<?php
$form = ActiveForm::begin([
    'action' => Url::to(['informacion/guardar']),
    'id' => 'informacion',
    'layout' => 'horizontal',
]);
?>

<input type="hidden" name="estudio_id" value="<?php echo $estudio->id; ?>">
<input type="hidden" name="seccion_id" value="<?php echo $info->seccion->id; ?>">

<?php
echo Summernote::widget([
    'id' => 'texto',
    'name' => 'texto',
    'value' => ($info and $info->texto) ? HtmlPurifier::process(
        $info->texto,
        [
            'Attr.ForbiddenClasses' => ['Apple-interchange-newline', 'Apple-converted-space',
                'Apple-paste-as-quotation', 'Apple-style-span', 'Apple-tab-span',
                'MsoChpDefault', 'MsoListParagraphCxSpFirst', 'MsoListParagraphCxSpLast',
                'MsoListParagraphCxSpMiddle', 'MsoNormal', 'MsoNormalTable', 'MsoPapDefault', 'western', ],
            'CSS.ForbiddenProperties' => ['border', 'border-bottom', 'border-left', 'border-right', 'border-top',
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
        'lang' => $locale,
        'placeholder' => Yii::t('cati', 'Introduzca sus comentarios'),
        'toolbar' => [
            ["style", ["style"]],
            ["font", ["bold", "underline", "clear"]],
            ["fontname", ["fontname"]],
            ["color", ["color"]],
            ["para", ["ul", "ol", "paragraph"]],
            ["table", ["table"]],
            ["insert", ["link", "picture"]],  # Quito "video", pues HtmlPurifier por omisión no permite iframes
            ["view", ["fullscreen", "codeview", "help"]]
        ],
    ],
]) . "\n\n";

echo '<hr>';
echo Html::submitButton(
    '<span class="glyphicon glyphicon-check"></span> ' . ($info ? Yii::t('cruds', 'Save') : Yii::t('cruds', 'Create')),
    [
        'id' => 'save-informacion',
        'class' => 'btn btn-success',
    ]
);
ActiveForm::end();
