<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Delegados coordinadores');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/calidad']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
\yii\widgets\Pjax::begin([
    'id' => 'pjax-main',
    'enableReplaceState' => false,
    'linkSelector' => '#pjax-main ul.pagination a, th a',
    'clientOptions' => ['pjax:success' => 'function() { alert("yo"); }']
]);
?>

<div class='table-responsive'>
    <?php
    $dataProvider = new ArrayDataProvider([
        'allModels' => $planes,
        'pagination' => false,  // ['pageSize' => 10],
        'sort' => [
            'attributes' => ['estudio.id_nk', 'estudio.nombre', 'centro.nombre', 'id_nk'],  // , 'nombre_delegado', 'email_delegado'],
            'defaultOrder' => [
                'estudio.nombre' => SORT_ASC,
                'centro.nombre' => SORT_ASC,
            ],
        ],
    ]);

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'estudio.id_nk',
                'label' => Yii::t('cruds', 'Cód. estudio'),
                'headerOptions' => [  // HTML attributes for the header cell tag
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'title' => Yii::t('gestion', 'Código del estudio'),
                ],
            ], [
                'attribute' => 'estudio.nombre',
                'label' => Yii::t('cruds', 'Estudio'),
                'value' => function ($plan) {
                    return Html::a(
                        $plan->estudio->nombre,
                        ['estudio/ver', 'id' => $plan->estudio->id_nk, 'anyo_academico' => $plan->anyo_academico]
                    );
                },
                'format' => 'html',
            ], [
                'attribute' => 'centro.nombre',
                'label' => Yii::t('cruds', 'Centro'),
            ], [
                'attribute' => 'id_nk',
                'headerOptions' => [  // HTML attributes for the header cell tag
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'title' => Yii::t('gestion', 'Código del plan'),
                ],
            ], /* [
                'attribute' => 'nombre_delegado',
                'label' => Yii::t('cruds', 'Nombre y apellidos'),
            ], [
                'attribute' => 'email_delegado',
                'format' => 'email', // See http://www.yiiframework.com/doc-2.0/guide-output-formatting.html
                'label' => Yii::t('cruds', 'Correo electrónico'),
            ], */ [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'lista' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('gestion', 'Ver/Editar los delegados'),
                            'aria-label' => Yii::t('gestion', 'Ver/Editar los delegados'),
                            'data-pjax' => '0',
                        ];

                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
                    },
                ],
                // 'controller' => 'gestion',
                'template' => '{lista}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    $params = ["{$action}-delegados-plan", 'plan_id' => $model->id];

                    return Url::toRoute($params);
                },
                // visibleButtons => ...,
                'contentOptions' => ['nowrap' => 'nowrap'],
            ],
        ],
        // 'headerRowOptions' => ['class' => 'x'],
        'options' => ['class' => 'cabecera-azul'],
        // 'pager' => ...,
        // 'summary' => '', // Do not show `Showing 1-19 of 19 items'.
        'tableOptions' => ['class' => 'table table-striped table-hover'],
    ]);
    ?>
</div>

<?php \yii\widgets\Pjax::end() ?>