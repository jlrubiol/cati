<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
*/

$this->title = Yii::t('models', 'Estudios');
$this->params['breadcrumbs'][] = $this->title;

if (isset($actionColumnTemplates)) {
$actionColumnTemplate = implode(' ', $actionColumnTemplates);
    $actionColumnTemplateString = $actionColumnTemplate;
} else {
Yii::$app->view->params['pageButtons'] = Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('cruds', 'New'), ['create'], ['class' => 'btn btn-success']);
    $actionColumnTemplateString = "{view} {update} {delete}";
}
$actionColumnTemplateString = '<div class="action-buttons">'.$actionColumnTemplateString.'</div>';
?>
<div class="giiant-crud estudio-index">

    <?php
//         ?>

    
    <?php \yii\widgets\Pjax::begin(['id'=>'pjax-main', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-main ul.pagination a, th a', 'clientOptions' => ['pjax:success'=>'function(){alert("yo")}']]) ?>

    <h1>
        <?= Yii::t('models', 'Estudios') ?>
        <small>
            List
        </small>
    </h1>
    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('cruds', 'New'), ['create'], ['class' => 'btn btn-success']) ?>
        </div>

        <div class="pull-right">

                                                                                                                                                                                                                                                                                    
            <?= 
            \yii\bootstrap\ButtonDropdown::widget(
            [
            'id' => 'giiant-relations',
            'encodeLabel' => false,
            'label' => '<span class="glyphicon glyphicon-paperclip"></span> ' . Yii::t('cruds', 'Relations'),
            'dropdown' => [
            'options' => [
            'class' => 'dropdown-menu-right'
            ],
            'encodeLabels' => false,
            'items' => [
            [
                'url' => ['rama/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('models', 'Rama'),
            ],
                                [
                'url' => ['tipo-estudio/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('models', 'Tipo Estudio'),
            ],
                                [
                'url' => ['informe-publicado/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('models', 'Informe Publicado'),
            ],
                                [
                'url' => ['informe-respuesta/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('models', 'Informe Respuesta'),
            ],
                                [
                'url' => ['linea/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('models', 'Linea'),
            ],
                                [
                'url' => ['plan/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('models', 'Plan'),
            ],
                                [
                'url' => ['plan-publicado/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('models', 'Plan Publicado'),
            ],
                                [
                'url' => ['plan-respuesta/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('models', 'Plan Respuesta'),
            ],
                                [
                'url' => ['estudio-lang/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('models', 'Estudio Lang'),
            ],
                    
]
            ],
            'options' => [
            'class' => 'btn-default'
            ]
            ]
            );
            ?>
        </div>
    </div>

    <hr />

    <div class="table-responsive">
        <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => [
        'class' => yii\widgets\LinkPager::class,
        'firstPageLabel' => Yii::t('cruds', 'First'),
        'lastPageLabel' => Yii::t('cruds', 'Last'),
        ],
                'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
        'headerRowOptions' => ['class'=>'x'],
        'columns' => [
                [
            'class' => 'yii\grid\ActionColumn',
            'template' => $actionColumnTemplateString,
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    $options = [
                        'title' => Yii::t('cruds', 'View'),
                        'aria-label' => Yii::t('cruds', 'View'),
                        'data-pjax' => '0',
                    ];
                    return Html::a('<span class="glyphicon glyphicon-file"></span>', $url, $options);
                }
            ],
            'urlCreator' => function($action, $model, $key, $index) {
                // using the column name as key, not mapping to 'id' like the standard generator
                $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string) $key];
                $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;
                return Url::toRoute($params);
            },
            'contentOptions' => ['nowrap'=>'nowrap']
        ],
			// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
			[
			    'class' => yii\grid\DataColumn::class,
			    'attribute' => 'tipoEstudio_id',
			    'value' => function ($model) {
			        if ($rel = $model->tipoEstudio) {
			            return Html::a($model->tipoEstudio_id, ['tipo-estudio/view', 'id' => $model->tipoEstudio_id,], ['data-pjax' => 0]) . " ({$rel})";
			        } else {
			            return '';
			        }
			    },
			    'format' => 'raw',
			],
			'activo',
			'anyo_academico',
			'id_nk',
            /* 'anyos_evaluacion', */
            /* 'fecha_implantacion', */
            /* 'fecha_acreditacion', */
			'codigo_mec',
			// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
			[
			    'class' => yii\grid\DataColumn::class,
			    'attribute' => 'rama_id',
			    'value' => function ($model) {
			        if ($rel = $model->rama) {
			            return Html::a($rel->id, ['rama/view', 'id' => $rel->id,], ['data-pjax' => 0]);
			        } else {
			            return '';
			        }
			    },
			    'format' => 'raw',
			],
			'nombre_coordinador',
			/*'email_coordinador:email',*/
        ],
        ]); ?>
    </div>

</div>


<?php \yii\widgets\Pjax::end() ?>


