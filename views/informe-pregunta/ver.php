<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\widgets\DetailView;

$anyo = $pregunta->anyo;
switch ($pregunta->tipo) {
    case 'grado-master':
        $nombre_lista = Yii::t('gestion', 'Informes de Grado y Máster');
        break;
    case 'doctorado':
        $nombre_lista = Yii::t('gestion', 'Informes de Doctorado');
        break;
    case 'iced':
        $nombre_lista = Yii::t('gestion', 'Informe de la Calidad de los Estudios de Doctorado');
        break;
    default:
        throw new NotFoundHttpException(sprintf(
            Yii::t('cati', 'No se ha encontrado el tipo de estudio «%s».  ☹'),
            $tipo
        ));
}
$this->title = sprintf(Yii::t('gestion', 'Apartado %s'), $pregunta->apartado);
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => $nombre_lista . ' ' . $anyo . '/' . ($anyo + 1),
    'url' => ['gestion/lista-informes', 'anyo' => $anyo, 'tipo' => $pregunta->tipo],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('models', 'Apartados'),
    'url' => ['informe-pregunta/lista', 'anyo' => $anyo, 'tipo' => $pregunta->tipo],
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
        'model' => $pregunta,
        'attributes' => [
            [
                'attribute' => 'anyo',
            ], [
                'attribute' => 'apartado',
            ], [
                'attribute' => 'editable',
                'format' => 'boolean',
            ], [
                'attribute' => 'editable_1',
                'format' => 'boolean',
            ], [
                'attribute' => 'invisible_1',
                'format' => 'boolean',
            ], [
                'attribute' => 'invisible_3',
                'format' => 'boolean',
            ], [
                'attribute' => 'tabla',
                'format' => 'html',
            ], [
                'attribute' => 'titulo',
                'format' => 'html',
            ], [
                'attribute' => 'info',
                'format' => 'html',
            ], [
                'attribute' => 'explicacion',
                'format' => 'html',
            ], [
                'attribute' => 'texto_comun',
                'format' => 'html',
                'value' => function ($model) { return nl2br($model->texto_comun); },
            ],
        ],
    ]);

    echo Html::a(
        '<span class="glyphicon glyphicon-pencil"></span> ' .  // Button
        Yii::t('gestion', 'Editar'),
        ['editar', 'id' => $pregunta->id],
        [
            'id' => 'editar',
            'class' => 'btn btn-info',
        ]
    ) . " &nbsp; \n";
    ?>
</div>
