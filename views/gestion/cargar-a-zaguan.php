<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Cargar a Zaguán');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
$siguiente_anyo = $anyo + 1;
$siguesigue_anyo = $siguiente_anyo + 1;
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr>

<h2><?php printf('%s %d', Yii::t('gestion', 'Documentos campaña'), $siguiente_anyo); ?></h2>

<p><?php echo Html::a(
    Yii::t('gestion', 'Informes de Evaluación de la Calidad'),
    ['informe/cargar-a-zaguan', 'anyo' => $anyo, 'tipo' => 'grado-master'],
    ['class' => 'btn btn-success']
); ?></p>

<p><?php echo Html::a(
    Yii::t('gestion', "Planes de innovación y mejora (para el curso {$siguiente_anyo}/{$siguesigue_anyo})"),
    ['plan-mejora/cargar-a-zaguan', 'anyo' => $anyo, 'tipo' => 'grado-master'],
    ['class' => 'btn btn-success']
); ?></p>
