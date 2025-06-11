<?php
/**
 * Fragmento de vista del formulario para añadir o editar una página de información de la web.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use marqu3s\summernote\Summernote;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

?>

<div class="pagina-form">

    <?php
    $form = ActiveForm::begin([
        'id' => $model->formName(),
        'layout' => 'default',
        'enableClientValidation' => true,
        'errorSummaryCssClass' => 'error-summary alert alert-error',
    ]);
    ?>

    <!-- attribute id -->
    <?php echo "\n" . Html::activeHiddenInput($model, 'id', ['value' => $model->id]) . "\n"; ?>

    <!-- attribute language -->
    <?php echo $form->field($model, 'language')
    ->textInput([
        'maxlength' => true,
        // 'placeholder' => '',
        'readonly' => 'readonly',
        'title' => Yii::t('gestion', 'Código ISO 639-1 del idioma.'),
    ])->hint('Ej: es, en...') . "<br>\n"; ?>

    <!-- attribute titulo -->
    <?php echo $form->field($model, 'titulo')->textInput(['maxlength' => true]) . "<br>\n"; ?>

    <!-- attribute cuerpo -->
    <?php echo $form->field($model, 'cuerpo')
    ->widget(
        Summernote::className(),
        [
            'clientOptions' => [
                'placeholder' => Yii::t('gestion', 'Introduzca el texto de la página'),
                'lang' => Yii::$app->catilanguage->getLocale(Yii::$app->language),
                'height' => 400,
            ],
            'options' => [
                'value' => HtmlPurifier::process($model->cuerpo, [
                    'Attr.ForbiddenClasses' => ['Apple-interchange-newline', 'Apple-converted-space',
                        'Apple-paste-as-quotation', 'Apple-style-span', 'Apple-tab-span',
                        'MsoChpDefault', 'MsoListParagraphCxSpFirst', 'MsoListParagraphCxSpLast',
                        'MsoListParagraphCxSpMiddle', 'MsoNormal', 'MsoNormalTable', 'MsoPapDefault', 'western', ],
                    'CSS.ForbiddenProperties' => ['border', 'border-bottom', 'border-left', 'border-right',
                        'border-top', 'font', 'font-family', 'font-size', 'font-weight', 'height', 'line-height',
                        'margin', 'margin-bottom', 'margin-left', 'margin-right', 'margin-top',
                        'padding', 'padding-bottom', 'padding-left', 'padding-right', 'padding-top',
                        'text-autospace', 'text-indent', 'width', ],
                    'HTML.ForbiddenAttributes' => ['align', 'background', 'bgcolor', 'border',
                        'cellpadding', 'cellspacing', 'height', 'hspace', 'noshade', 'nowrap',
                        'rules', 'size', 'valign', 'vspace', 'width', ],
                    'HTML.ForbiddenElements' => ['font'],
                    // 'data' permite incrustar imágenes en base64
                    'URI.AllowedSchemes' => ['data' => true, 'http' => true, 'https' => true, 'mailto' => true],
                ]),
            ],
        ]
    ); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="form-group">
            <?php echo Html::submitButton(
                '<span class="glyphicon glyphicon-check"></span> ' .
                ($model->isNewRecord ? Yii::t('cruds', 'Create') : Yii::t('cruds', 'Save')),
                [
                    'id' => 'save-' . $model->formName(),
                    'class' => 'btn btn-success',
                ]
            );
            ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>