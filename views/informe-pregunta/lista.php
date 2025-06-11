<?php
/**
 * Vista de la lista de los apartados de los informes de evaluación de la calidad.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

switch ($tipo) {
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
$this->title = Yii::t('gestion', 'Apartados');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => $nombre_lista . ' ' . $anyo . '/' . ($anyo + 1),
    'url' => ['gestion/lista-informes', 'anyo' => $anyo, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php

echo Html::a(
    '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('cruds', 'Nuevo apartado'),
    ['crear', 'anyo' => $anyo, 'tipo' => $tipo],
    ['class' => 'btn btn-info']
);
?>

<br><br>

<div class="table-responsive">
    <?php
    $dataProvider = new ArrayDataProvider([
        'allModels' => $preguntas,
        'pagination' => false, // ['pageSize' => 10],
        'sort' => [
            'attributes' => ['anyo', 'apartado'],
            'defaultOrder' => [
                'anyo' => SORT_DESC,
                'apartado' => SORT_ASC,
            ],
        ],
    ]);

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'anyo',
                // 'label' => Yii::t('cruds', 'Año'),
            ], [
                'attribute' => 'apartado',
                // 'label' => Yii::t('cruds', 'Apartado'),
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
                'attribute' => 'titulo',
            ], [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'ver' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('gestion', 'Ver la pregunta'),
                            'aria-label' => Yii::t('gestion', 'Editar la pregunta'),
                            'data-pjax' => '0',
                        ];

                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
                    },
                    'editar' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('gestion', 'Editar la pregunta'),
                            'aria-label' => Yii::t('gestion', 'Editar la pregunta'),
                            'data-pjax' => '0',
                        ];

                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                    },
                    'borrar' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('gestion', 'Borrar la pregunta'),
                            'aria-label' => Yii::t('gestion', 'Borrar la pregunta'),
                            'data-pjax' => '0',
                        ];

                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                    },
                ],
                // 'controller' => 'gestion',
                'template' => '{ver} {editar} {borrar}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    $params = [$action, 'id' => $model->id];

                    return Url::toRoute($params);
                },
                // visibleButtons => ...,
                'contentOptions' => ['nowrap' => 'nowrap'],
            ],
        ],
        // 'summary' => '', // Do not show `Showing 1-19 of 19 items'.
    ]);
    ?>
</div>
