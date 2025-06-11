<?php

use app\models\Estudio;
use app\assets\ChartJsAsset;

$bundle = ChartJsAsset::register($this);

$this->title = Yii::t('cati', 'Resultados globales de tasas de abandono/graduación') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Tasas de abandono/graduación');
?>
<script src="<?php echo $bundle->baseUrl  ?>/Chart.bundle.js"></script>

<?php echo $this->render('_chart_config') ?>

<h1><?php echo Yii::t('cati', 'Tasas de abandono/graduación') ?> &nbsp; <a href="<?php echo Yii::getAlias('@web') ?>/pdf/definiciones_web_estudios_globales_v12.pdf"><span class="icon-info-with-circle"></span></a></h1>

<hr><br>

<?php
echo $this->render('_globales_abandono', [
    'apartado' => $apartado ?? null,
    'estudio' => $estudio,
    'globales_abandono' => $globales_abandono,
    'centros' => $centros,
    'num_tabla' => $num_tabla ?? null,
]);
