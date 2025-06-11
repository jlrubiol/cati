<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

$this->title = HtmlPurifier::process($model->titulo);

$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Information'), 'url' => ['ver', 'id' => 1]];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?php echo Html::encode($this->title); ?></h1>

<hr>

<?php echo HtmlPurifier::process(
    $model->cuerpo,
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
); ?>
