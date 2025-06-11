<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = Yii::t('gestion', 'Acreditación del estudio');

$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Acreditación de los estudios'),
    'url' => ['gestion/ver-acreditacion-estudios'],
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
        'model' => $acreditacion,
        'attributes' => [
            'nk',
            [
                'label' => Yii::t('models', 'Nombre'),
                'value' => function ($ac) {
                    return $ac->estudio->nombre;
                },
            ],
            'cod_ruct',
            [
                'label' => 'Es interuniversitario',
                'value' => function ($ac) { return $ac->esInteruniversitario; },
                'format' => 'boolean',
            ],
            [
                'label' => 'Coordina UZ',
                'value' => function ($ac) { return $ac->esInteruniversitario ? $ac->coordinaUz : null; },
                'format' => 'boolean',
            ],
            'fecha_verificacion',
            'fecha_implantacion',
            [
                'label' => 'Fecha de última renovación',
                'attribute' => 'fechaAcreditacion',
            ],
            'anyos_validez',
            [
                'label' => Yii::t('cruds', 'Fecha próxima renovación'),
                'attribute' => 'proximaRenovacion',
            ]
        ],
    ]);

    echo Html::a(
        '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('gestion', 'Actualizar'),
        ['actualizar-acreditacion-estudio', 'nk' => $acreditacion->nk],
        ['id' => 'actualizar-acreditacion-estudio', 'class' => 'btn btn-info']  // Button
    ) . " &nbsp; \n";
    ?>
</div>
