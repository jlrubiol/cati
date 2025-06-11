<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var app\models\Doctorado $model
*/
$copyParams = $model->attributes;

$this->title = Yii::t('models', 'Doctorado');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Doctorados'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('cruds', 'View');
?>
<div class="giiant-crud doctorado-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?= \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <h1>
        <?= Yii::t('models', 'Doctorado') ?>
        <small>
            <?= Html::encode($model->id) ?>
        </small>
    </h1>


    <div class="clearfix crud-navigation">

        <!-- menu buttons -->
        <div class='pull-left'>
            <?= Html::a(
            '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('cruds', 'Edit'),
            [ 'update', 'id' => $model->id],
            ['class' => 'btn btn-info']) ?>

            <?= Html::a(
            '<span class="glyphicon glyphicon-copy"></span> ' . Yii::t('cruds', 'Copy'),
            ['create', 'id' => $model->id, 'Doctorado'=>$copyParams],
            ['class' => 'btn btn-success']) ?>

            <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('cruds', 'New'),
            ['create'],
            ['class' => 'btn btn-success']) ?>
        </div>

        <div class="pull-right">
            <?= Html::a('<span class="glyphicon glyphicon-list"></span> '
            . Yii::t('cruds', 'Full list'), ['index'], ['class'=>'btn btn-default']) ?>
        </div>

    </div>

    <hr/>

    <?php $this->beginBlock('app\models\Doctorado'); ?>

    
    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
            'cod_estudio',
        'cod_plan',
        'cod_centro',
        'ano_academico',
        'plazas_ofertadas',
        'num_solicitudes',
        'alumnos_nuevo_ingreso',
        'alumnos_ni_tiempo_parcial',
        'alumnos_matriculados',
        'alumnos_act_transv',
        'numero_profesores',
        'numero_profesores_uz',
        'numero_profesores_nouz',
        'num_sexenios_profesores',
        'numero_expertos_int_trib',
        'numero_miembros_trib',
        'numero_directores_tesis_leidas',
        'numero_proy_inter_vivos',
        'numero_proy_nac_vivos',
        'numero_tesis_tiempo_completo',
        'numero_tesis_tiempo_parcial',
        'numero_alu_encuesta_global_1',
        'numero_alu_encuesta_global_2',
        'numero_alu_encuesta_global_3',
        'numero_alu_encuesta_global_4',
        'numero_alu_encuesta_global_5',
        'numero_prof_encuesta_global_1',
        'numero_prof_encuesta_global_2',
        'numero_prof_encuesta_global_3',
        'numero_prof_encuesta_global_4',
        'numero_prof_encuesta_global_5',
        'porc_est_previo_nouz',
        'porc_ni_comp_formacion',
        'porc_ni_tiempo_parcial',
        'porc_matr_extrajeros',
        'porc_alumnos_beca',
        'porc_alumnos_beca_distinta',
        'porc_matri_tiempo_parcial',
        'porc_alumnos_mov_out_ano',
        'porc_alumnos_mov_out_gen',
        'porc_sexenios_vivos',
        'porc_prof_tiempo_completo',
        'porc_dir_tes_le_sexenios_vivos',
        'numero_publ_indexadas',
        'numero_publ_no_indexadas',
        'duracion_media_tiempo_completo',
        'duracion_media_tiempo_parcial',
        'porc_abandono',
        'porc_tesis_no_pri_prorroga',
        'porc_tesis_no_seg_prorroga',
        'porc_tesis_cum_laude',
        'porc_tesis_men_internacional',
        'porc_tesis_men_doc_industrial',
        'porc_tesis_cotutela',
        'num_medio_resultados_tesis',
        'fecha_carga',
    ],
    ]); ?>

    
    <hr/>

    <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('cruds', 'Delete'), ['delete', 'id' => $model->id],
    [
    'class' => 'btn btn-danger',
    'data-confirm' => '' . Yii::t('cruds', 'Are you sure to delete this item?') . '',
    'data-method' => 'post',
    ]); ?>
    <?php $this->endBlock(); ?>


    
    <?= Tabs::widget(
                 [
                     'id' => 'relation-tabs',
                     'encodeLabels' => false,
                     'items' => [
 [
    'label'   => '<b class=""># '.Html::encode($model->id).'</b>',
    'content' => $this->blocks['app\models\Doctorado'],
    'active'  => true,
],
 ]
                 ]
    );
    ?>
</div>
