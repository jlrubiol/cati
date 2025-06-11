<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\Calendario;

/*
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\CalendarioSearch $searchModel
 */

$this->title = Yii::t('models', 'Fechas clave');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);

// $actionColumnTemplateString = '{view} {update} {delete}';
$actionColumnTemplateString = '{update} {delete}';
$actionColumnTemplateString = '<div class="action-buttons">' . $actionColumnTemplateString . '</div>';
?>

<h1><?php echo Yii::t('cati', 'Fechas clave'); ?></h1>
<hr><br>

<?php
echo yii\bootstrap\Alert::widget([
    'body' => "<span class='glyphicon glyphicon-info-sign'></span>" . nl2br(Yii::t('gestion', 'La web de estudios pasará a mostrar la información del nuevo curso en la fecha que se indique.
    En <em>año</em> se debe indicar el de comienzo del curso.')),
    'options' => ['class' => 'alert-info'],
]) . "\n\n";
echo yii\bootstrap\Alert::widget([
    'body' => "<span class='glyphicon glyphicon-exclamation-sign'></span>" . nl2br(Yii::t('gestion', '<strong>Antes</strong> de cambiar el año, probablemente desee <strong>clonar los apartados</strong> tanto del IEC como del PAIM (y del ICED, en el caso de Doctorado).')),
    'options' => ['class' => 'alert-warning'],
]) . "\n\n";
?>

<div class="table-responsive">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'anyo',
            [
                'attribute' => 'fecha',
                'filter' => '',
                'format' => ['date', 'long'],
            ], [
                'attribute' => 'evento',
                'filter' => Html::dropDownList(
                    'CalendarioSearch[evento]',
                    Yii::$app->request->get('CalendarioSearch')['evento'] ?? null,
                    Calendario::EVENTOS,
                    ['prompt' => Yii::t('cati', 'Todos')]
                ),
                'value' => function ($registro) {
                    return isset(Calendario::EVENTOS[$registro['evento']]) ? Calendario::EVENTOS[$registro['evento']] : '';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $actionColumnTemplateString,
                'buttons' => [
                    /*
                    'view' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('cruds', 'View'),
                            'aria-label' => Yii::t('cruds', 'View'),
                            'data-pjax' => '0',
                        ];

                        return Html::a('<span class="glyphicon glyphicon-file"></span>', $url, $options);
                    },
                    */
                ],
                // 'controller' => 'gestion',
                'urlCreator' => function ($action, $model, $key, $index) {
                    // using the column name as key, not mapping to 'id' like the standard generator
                    $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string) $key];
                    $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;

                    return Url::toRoute($params);
                },
                // visibleButtons => ...,
                'contentOptions' => ['nowrap' => 'nowrap'],
            ],
        ],

        'headerRowOptions' => ['class' => 'x'],
        'options' => ['class' => 'cabecera-azul'],
        'pager' => [
            'class' => yii\widgets\LinkPager::className(),
            'firstPageLabel' => Yii::t('cruds', 'First'),
            'lastPageLabel' => Yii::t('cruds', 'Last'),
        ],
        // 'summary' => '', // Do not show `Showing 1-19 of 19 items'.
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
    ]); ?>
</div>

<div class="pull-left">
    <?php echo Html::a(
        '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('cati', 'Añadir fecha'),
        ['create'],
        ['class' => 'btn btn-success']
    ); ?>
</div><br><br>
