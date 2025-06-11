<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Resultado de la carga');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Cargar a Zaguán'),
    'url' => ['gestion/cargar-a-zaguan', 'anyo' => $anyo, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<p><b><?php echo Yii::t('gestion', 'Carga enviada.'); ?></b></p>

<p><?php echo Yii::t('gestion', 'La respuesta de Zaguán fue:'); ?></p>

<div class="alert alert-info fade in">
    <?php echo Html::encode($respuesta) . "\n"; ?>
</div>