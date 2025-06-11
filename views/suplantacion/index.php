<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Suplantación de un usuario');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
echo Html::beginForm('login', 'post', ['class' => 'form-horizontal']) . "\n\n";

echo Html::beginTag('div', ['class' => 'form-group']) . "\n";
echo Html::label(Yii::t('app', 'NIP'), 'username', ['class' => 'control-label col-sm-3']) . "\n";
echo Html::beginTag('div', ['class' => 'col-sm-6']) . "\n";
echo Html::textInput('username', '', ['class' => 'form-control', 'placeholder' => Yii::t('gestion', 'NIP a suplantar')]) . "\n";
echo Html::tag('p', nl2br(Yii::t('gestion', "Introduzca el NIP del usuario al que desea suplantar.")), ['class' => 'help-block']) . "\n";
echo Html::endTag('div') . "\n";
echo Html::endTag('div') . "\n\n";


echo Html::beginTag('div', ['class' => 'form-group']) . "\n";
echo Html::beginTag('div', ['class' => 'col-lg-offset-3 col-lg-9']) . "\n";
echo Html::submitButton(Yii::t('gestion', 'Suplantar'), ['class' => 'btn btn-success']) . "\n";
echo Html::endTag('div') . "\n";
echo Html::endTag('div') . "\n";

echo Html::endForm() . "\n\n";
