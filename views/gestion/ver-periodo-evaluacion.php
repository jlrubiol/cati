<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = Yii::t('gestion', 'Periodo de evaluación');

$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Periodos de evaluación de los estudios'). ' ' . $estudio->anyo_academico . '/' . ($estudio->anyo_academico + 1),
    'url' => ['gestion/ver-periodos-evaluacion', 'anyo' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = $estudio->nombre;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<div style="width: 100%">
    <?php
    echo DetailView::widget([
        'model' => $estudio,
        'attributes' => [
            'id_nk',
            'nombre',
            'anyos_evaluacion',
        ],
    ]);

    echo Html::a(
        '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('gestion', 'Actualizar'),
        ['actualizar-periodo-evaluacion', 'id' => $estudio->id],
        ['id' => 'actualizar-periodo-evaluacion', 'class' => 'btn btn-info']  // Button
    ) . " &nbsp; \n";
    ?>
</div>
