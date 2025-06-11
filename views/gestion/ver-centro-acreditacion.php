<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = Yii::t('gestion', 'Acreditación institucional del centro');

$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Acreditación institucional de los centros'),
    'url' => ['gestion/ver-centros-acreditacion'],
];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<div style="width: 100%">
    <?php
    echo DetailView::widget([
        'model' => $centro,
        'attributes' => [
            'nombre',
            'direccion',
            'municipio',
            'telefono',
            'url',
            'nombre_decano',
            'email_decano',
            'acreditacion_url',
            'fecha_acreditacion',
            'anyos_validez',
            [
                'label' => Yii::t('cati', 'Fecha próxima renovación'),
                'attribute' => 'proximaRenovacion',
            ],
        ],
    ]);

    echo Html::a(
        '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('gestion', 'Actualizar'),
        ['actualizar-centro-acreditacion', 'id' => $centro->id],
        ['id' => 'actualizar-centro-acreditacion', 'class' => 'btn btn-info']  // Button
    ) . " &nbsp; \n";
    ?>
</div>
