<?php

use app\models\Estudio;
use app\assets\ChartJsAsset;

$bundle = ChartJsAsset::register($this);

$this->title = Yii::t('cati', 'Resultados globales de duración media graduados') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Duración media graduados');
?>
<script src="<?php echo $bundle->baseUrl  ?>/Chart.bundle.js"></script>

<?php echo $this->render('_chart_config') ?>

<h1><?php echo Yii::t('cati', 'Duración media graduados') ?> &nbsp; <a href="<?php echo Yii::getAlias('@web') ?>/pdf/definiciones_web_estudios_globales_v12.pdf"><span class="icon-info-with-circle"></span></a></h1>

<hr><br>

<?php
echo $this->render('_globales_duracion', [
    'estudio' => $estudio,
    'globales' => $globales,
    'centros' => $centros,
]);
