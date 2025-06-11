<?php
/**
 * Vista de la lista de los apartados de los planes de innovación y mejora.
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

$lista = 'gestion/lista-planes';
$nombre_lista = Yii::t('gestion', 'Planes de innovación y mejora');

$this->title = Yii::t('gestion', 'Apartados');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => $nombre_lista . ' ' . $anyo . '/' . ($anyo + 1),
    'url' => [$lista, 'anyo' => $anyo, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = $this->title;

// Cambiar el color de fondo
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php echo Html::a(
    '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('cruds', 'Nuevo apartado'),
    ['crear', 'anyo' => $anyo, 'tipo' => $tipo],
    ['class' => 'btn btn-info']
); ?><br><br>


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

\yii\widgets\Pjax::begin(
    [
        'id' => 'pjax-main',
        'enableReplaceState' => false,
        'linkSelector' => '#pjax-main ul.pagination a, th a',
        // 'clientOptions' => ['pjax:success' => 'function() { alert("yo"); }'],
    ]
);

echo "<div class='table-responsive'>";
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'anyo',
        'apartado',
        'titulo',
        [
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
                        'data-confirm' => Yii::t('gestion', '¿Seguro que desea eliminar esta pregunta?'),
                        'data-method' => 'post',
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
    'options' => ['class' => 'cabecera-azul'],
    // 'summary' => '', // Do not show `Showing 1-19 of 19 items'.
    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
]);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end();
