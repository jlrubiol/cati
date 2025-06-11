<?php
/**
 * Vista de un apartado de los planes de innovación y mejora.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$anyo = $pregunta->anyo;
$lista = 'gestion/lista-planes';
$nombre_lista = Yii::t('gestion', 'Planes de innovación y mejora');

$this->title = sprintf(Yii::t('gestion', 'Apartado %s'), $pregunta->apartado);
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => $nombre_lista . ' ' . $anyo . '/' . ($anyo + 1),
    'url' => [$lista, 'anyo' => $anyo, 'tipo' => $pregunta->tipo],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('models', 'Apartados'),
    'url' => ['plan-pregunta/lista', 'anyo' => $anyo, 'tipo' => $pregunta->tipo],
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
                'format' => 'html',
            ], [
                'attribute' => 'titulo',
                'format' => 'html',
            ], [
                'attribute' => 'explicacion',
                'format' => 'html',
            ], [
                'attribute' => 'atributos',
                'format' => 'html',
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
