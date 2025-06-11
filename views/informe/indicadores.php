<?php

use yii\helpers\Html;
use app\assets\ChartJsAsset;

$bundle = ChartJsAsset::register($this);

$this->title = Yii::t('cati', 'Análisis de los indicadores del título') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Análisis de los indicadores del título');
?>
<script src="<?php echo $bundle->baseUrl; ?>/Chart.bundle.js"></script>

<?php echo $this->render('_chart_config') ?>

<h1><?php echo Html::encode($this->title); ?></h1>

<hr>

<?php
echo $this->render('_indicadores', [
    'apartado' => $apartado ?? null,
    'centros' => $centros,
    'estudio' => $estudio,
    'indicadores' => $indicadores,
    'num_tabla' => $num_tabla ?? null,
]);
