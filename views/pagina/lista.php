<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('models', 'Paginas');

$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Cambiar el color de fondo
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>

<hr>

<div class="table-responsive">
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'titulo',
            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'ver' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', 'View'),
                            'aria-label' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                        ];

                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
                    },
                    'editar' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('gestion', 'Editar'),
                            'aria-label' => Yii::t('gestion', 'Editar'),
                            'data-pjax' => '0',
                        ];

                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                    },
                ],
                'contentOptions' => ['nowrap' => 'nowrap'],
                'template' => '{ver} {editar}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    // using the column name as key, not mapping to 'id' like the standard generator
                    $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string) $key];
                    $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;

                    return Url::toRoute($params);
                },
                // visibleButtons => ...,
            ],
        ],
        // 'headerRowOptions' => ['class' => ''],
        'options' => ['class' => 'cabecera-azul'],
        'pager' => [
            'class' => yii\widgets\LinkPager::className(),
            'firstPageLabel' => Yii::t('cruds', 'First'),
            'lastPageLabel' => Yii::t('cruds', 'Last'),
        ],
        // 'summary' => '',  // No mostrar `Mostrando 1-19 de 19 elementos'.
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
    ]);
    ?>
</div>
