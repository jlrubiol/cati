<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use marqu3s\summernote\Summernote;

$this->title = Yii::t('cati', 'Edición de las notas del plan ') . $plan->id_nk . ' — ' . $plan->estudio->nombre;

$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/grado-master']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Editar notas de los planes'),
    'url' => ['gestion/lista-notas-planes'],
];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
$form = ActiveForm::begin([
    'action' => Url::to(['notas-plan/guardar']),
    'id' => 'notas-plan',
    'layout' => 'horizontal',
]);
?>

<input type="hidden" name="notas_id" value="<?php echo $notas->id; ?>">
<input type="hidden" name="plan_id_nk" value="<?php echo $plan->id_nk; ?>">


<?php
echo Summernote::widget([
    'id' => 'texto',
    'name' => 'texto',
    'value' => ($notas->texto) ? HtmlPurifier::process($notas->texto) : '',
    'clientOptions' => [
        'lang' => Yii::$app->catilanguage->getLocale(Yii::$app->language),
        'placeholder' => Yii::t('cati', 'Introduzca sus notas'),
    ],
]) . "\n\n";

echo '<hr>';
echo Html::submitButton(
    '<span class="glyphicon glyphicon-check"></span> ' . Yii::t('cruds', 'Save'),
    [
        'id' => 'save-notas',
        'class' => 'btn btn-success',
    ]
);
ActiveForm::end();
