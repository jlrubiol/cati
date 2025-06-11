<?php

use yii\helpers\Html;

$this->title = Yii::t('gestion', 'Actualizar datos académicos de Doctorado');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
echo yii\bootstrap\Alert::widget(
    [
        'body' => "<span class='glyphicon glyphicon-info-sign'></span>" . Yii::t('gestion', "Desde esta página puede importar desde DATUZ los datos académicos de un curso de Doctorado."),
        'options' => ['class' => 'alert-info'],
    ]
) . "\n\n";

echo Html::beginForm('', 'post', ['class' => 'form-horizontal']) . "\n\n";

echo Html::beginTag('div', ['class' => 'form-group']) . "\n";
echo Html::label(Yii::t('app', 'Año'), 'curso', ['class' => 'control-label col-sm-3']) . "\n";
echo Html::beginTag('div', ['class' => 'col-sm-6']) . "\n";
echo Html::textInput('curso', date('Y') - 1, ['class' => 'form-control', 'placeholder' => Yii::t('gestion', 'Año en el que comienza el curso')]) . "\n";
echo Html::tag('p', nl2br(Yii::t('gestion', "Introduzca el año en que se inicia el curso académico cuyos datos desea actualizar.\nPor ejemplo, para el curso 2018-2019, introduzca «2018».")), ['class' => 'help-block']) . "\n";
echo Html::endTag('div') . "\n";
echo Html::endTag('div') . "\n\n";

echo Html::beginTag('div', ['class' => 'form-group']) . "\n";
echo Html::beginTag('div', ['class' => 'col-lg-offset-3 col-lg-9']) . "\n";
echo Html::submitButton(Yii::t('gestion', 'Actualizar'), ['class' => 'btn btn-success']) . "\n";
echo Html::endTag('div') . "\n";
echo Html::endTag('div') . "\n";

echo Html::endForm() . "\n\n";
