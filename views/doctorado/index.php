<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
*/

$this->title = Yii::t('models', 'Doctorados');
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
<div class="giiant-crud doctorado-index">

    <?php
//         ?>

    
    <?php \yii\widgets\Pjax::begin(['id'=>'pjax-main', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-main ul.pagination a, th a', 'clientOptions' => ['pjax:success'=>'function(){alert("yo")}']]) ?>

    <h1>
        <?= Yii::t('models', 'Doctorados') ?>
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
        'class' => yii\widgets\LinkPager::className(),
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
			'cod_estudio',
			'cod_plan',
			'cod_centro',
			'ano_academico',
			'plazas_ofertadas',
			'num_solicitudes',
			'alumnos_nuevo_ingreso',
			/*'alumnos_ni_tiempo_parcial',*/
			/*'alumnos_matriculados',*/
			/*'alumnos_act_transv',*/
			/*'numero_profesores',*/
			/*'numero_profesores_uz',*/
			/*'numero_profesores_nouz',*/
			/*'num_sexenios_profesores',*/
			/*'numero_expertos_int_trib',*/
			/*'numero_miembros_trib',*/
			/*'numero_directores_tesis_leidas',*/
			/*'numero_proy_inter_vivos',*/
			/*'numero_proy_nac_vivos',*/
			/*'numero_tesis_tiempo_completo',*/
			/*'numero_tesis_tiempo_parcial',*/
			/*'numero_alu_encuesta_global_1',*/
			/*'numero_alu_encuesta_global_2',*/
			/*'numero_alu_encuesta_global_3',*/
			/*'numero_alu_encuesta_global_4',*/
			/*'numero_alu_encuesta_global_5',*/
			/*'numero_prof_encuesta_global_1',*/
			/*'numero_prof_encuesta_global_2',*/
			/*'numero_prof_encuesta_global_3',*/
			/*'numero_prof_encuesta_global_4',*/
			/*'numero_prof_encuesta_global_5',*/
			/*'porc_est_previo_nouz',*/
			/*'porc_ni_comp_formacion',*/
			/*'porc_ni_tiempo_parcial',*/
			/*'porc_matr_extrajeros',*/
			/*'porc_alumnos_beca',*/
			/*'porc_alumnos_beca_distinta',*/
			/*'porc_matri_tiempo_parcial',*/
			/*'porc_alumnos_mov_out_ano',*/
			/*'porc_alumnos_mov_out_gen',*/
			/*'porc_sexenios_vivos',*/
			/*'porc_prof_tiempo_completo',*/
			/*'porc_dir_tes_le_sexenios_vivos',*/
			/*'numero_publ_indexadas',*/
			/*'numero_publ_no_indexadas',*/
			/*'duracion_media_tiempo_completo',*/
			/*'duracion_media_tiempo_parcial',*/
			/*'porc_abandono',*/
			/*'porc_tesis_no_pri_prorroga',*/
			/*'porc_tesis_no_seg_prorroga',*/
			/*'porc_tesis_cum_laude',*/
			/*'porc_tesis_men_internacional',*/
			/*'porc_tesis_men_doc_industrial',*/
			/*'porc_tesis_cotutela',*/
			/*'num_medio_resultados_tesis',*/
			/*'fecha_carga',*/
        ],
        ]); ?>
    </div>

</div>


<?php \yii\widgets\Pjax::end() ?>


