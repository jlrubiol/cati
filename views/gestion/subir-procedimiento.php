<?php

// use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;

$max_filesize = ini_get('upload_max_filesize');

$this->title = Yii::t('gestion', 'Subir procedimiento de funcionamiento del SIGC');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<div class="procedimiento-form">

<?php
$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-error',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-2',
            // 'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-8',
            'error' => '',
            'hint' => '',
        ],
    ],
    'layout' => 'default',
    'options' => ['enctype' => 'multipart/form-data']
]);

// Requiere bower-asset/bootstrap-filestyle:~1.2.3 (bootstrap 3)
// que se usa vía assets/FilestyleAsset
echo $form->field($model, 'pdfFile')->label(false)->fileInput([
    'class' => 'btn filestyle',
    // 'data-badge' => false,
    'data-buttonBefore' => 'true',
    'data-buttonText' => Yii::t('cati', 'Seleccionar documento PDF'),
    // 'data-disabled' => 'true',
    'data-icon' => 'false',
    // 'data-iconName' => 'glyphicon glyphicon-folder-open',
    // 'data-input' => 'false',
    // 'data-placeholder' => ,
    // 'data-size' => 'sm',
    'accept' => '.pdf',
])->hint(Yii::t('cati', 'Tamaño máximo: ') . $max_filesize) . "\n";


echo $form->errorSummary($model) . "\n";

echo "<div class='form-group'>\n";
echo Html::a(
    Yii::t('cati', 'Cancelar'),
    ['//gestion/index'],
    ['class' => 'btn btn-default']
) . "&nbsp;\n&nbsp;";

echo Html::submitButton(
    '<span class="glyphicon glyphicon-check"></span> ' . Yii::t('cati', 'Guardar'),
    [
        'id' => 'save-' . $model->formName(),
        'class' => 'btn btn-success',
    ]
) . "\n";
echo "</div>\n";
ActiveForm::end();
?>
</div>