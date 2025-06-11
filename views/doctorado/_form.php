<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var app\models\Doctorado $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="doctorado-form">

    <?php $form = ActiveForm::begin([
    'id' => 'Doctorado',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger',
    'fieldConfig' => [
             'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
             'horizontalCssClasses' => [
                 'label' => 'col-sm-2',
                 #'offset' => 'col-sm-offset-4',
                 'wrapper' => 'col-sm-8',
                 'error' => '',
                 'hint' => '',
             ],
         ],
    ]
    );
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>
            

<!-- attribute cod_estudio -->
			<?= $form->field($model, 'cod_estudio')->textInput() ?>

<!-- attribute cod_plan -->
			<?= $form->field($model, 'cod_plan')->textInput() ?>

<!-- attribute cod_centro -->
			<?= $form->field($model, 'cod_centro')->textInput() ?>

<!-- attribute ano_academico -->
			<?= $form->field($model, 'ano_academico')->textInput() ?>

<!-- attribute plazas_ofertadas -->
			<?= $form->field($model, 'plazas_ofertadas')->textInput() ?>

<!-- attribute num_solicitudes -->
			<?= $form->field($model, 'num_solicitudes')->textInput() ?>

<!-- attribute alumnos_nuevo_ingreso -->
			<?= $form->field($model, 'alumnos_nuevo_ingreso')->textInput() ?>

<!-- attribute alumnos_ni_tiempo_parcial -->
			<?= $form->field($model, 'alumnos_ni_tiempo_parcial')->textInput() ?>

<!-- attribute alumnos_matriculados -->
			<?= $form->field($model, 'alumnos_matriculados')->textInput() ?>

<!-- attribute alumnos_act_transv -->
			<?= $form->field($model, 'alumnos_act_transv')->textInput() ?>

<!-- attribute numero_profesores -->
			<?= $form->field($model, 'numero_profesores')->textInput() ?>

<!-- attribute numero_profesores_uz -->
			<?= $form->field($model, 'numero_profesores_uz')->textInput() ?>

<!-- attribute numero_profesores_nouz -->
			<?= $form->field($model, 'numero_profesores_nouz')->textInput() ?>

<!-- attribute num_sexenios_profesores -->
			<?= $form->field($model, 'num_sexenios_profesores')->textInput() ?>

<!-- attribute numero_expertos_int_trib -->
			<?= $form->field($model, 'numero_expertos_int_trib')->textInput() ?>

<!-- attribute numero_miembros_trib -->
			<?= $form->field($model, 'numero_miembros_trib')->textInput() ?>

<!-- attribute numero_directores_tesis_leidas -->
			<?= $form->field($model, 'numero_directores_tesis_leidas')->textInput() ?>

<!-- attribute numero_proy_inter_vivos -->
			<?= $form->field($model, 'numero_proy_inter_vivos')->textInput() ?>

<!-- attribute numero_proy_nac_vivos -->
			<?= $form->field($model, 'numero_proy_nac_vivos')->textInput() ?>

<!-- attribute numero_tesis_tiempo_completo -->
			<?= $form->field($model, 'numero_tesis_tiempo_completo')->textInput() ?>

<!-- attribute numero_tesis_tiempo_parcial -->
			<?= $form->field($model, 'numero_tesis_tiempo_parcial')->textInput() ?>

<!-- attribute numero_alu_encuesta_global_1 -->
			<?= $form->field($model, 'numero_alu_encuesta_global_1')->textInput() ?>

<!-- attribute numero_alu_encuesta_global_2 -->
			<?= $form->field($model, 'numero_alu_encuesta_global_2')->textInput() ?>

<!-- attribute numero_alu_encuesta_global_3 -->
			<?= $form->field($model, 'numero_alu_encuesta_global_3')->textInput() ?>

<!-- attribute numero_alu_encuesta_global_4 -->
			<?= $form->field($model, 'numero_alu_encuesta_global_4')->textInput() ?>

<!-- attribute numero_alu_encuesta_global_5 -->
			<?= $form->field($model, 'numero_alu_encuesta_global_5')->textInput() ?>

<!-- attribute numero_prof_encuesta_global_1 -->
			<?= $form->field($model, 'numero_prof_encuesta_global_1')->textInput() ?>

<!-- attribute numero_prof_encuesta_global_2 -->
			<?= $form->field($model, 'numero_prof_encuesta_global_2')->textInput() ?>

<!-- attribute numero_prof_encuesta_global_3 -->
			<?= $form->field($model, 'numero_prof_encuesta_global_3')->textInput() ?>

<!-- attribute numero_prof_encuesta_global_4 -->
			<?= $form->field($model, 'numero_prof_encuesta_global_4')->textInput() ?>

<!-- attribute numero_prof_encuesta_global_5 -->
			<?= $form->field($model, 'numero_prof_encuesta_global_5')->textInput() ?>

<!-- attribute porc_est_previo_nouz -->
			<?= $form->field($model, 'porc_est_previo_nouz')->textInput() ?>

<!-- attribute porc_ni_comp_formacion -->
			<?= $form->field($model, 'porc_ni_comp_formacion')->textInput() ?>

<!-- attribute porc_ni_tiempo_parcial -->
			<?= $form->field($model, 'porc_ni_tiempo_parcial')->textInput() ?>

<!-- attribute porc_matr_extrajeros -->
			<?= $form->field($model, 'porc_matr_extrajeros')->textInput() ?>

<!-- attribute porc_alumnos_beca -->
			<?= $form->field($model, 'porc_alumnos_beca')->textInput() ?>

<!-- attribute porc_alumnos_beca_distinta -->
			<?= $form->field($model, 'porc_alumnos_beca_distinta')->textInput() ?>

<!-- attribute porc_matri_tiempo_parcial -->
			<?= $form->field($model, 'porc_matri_tiempo_parcial')->textInput() ?>

<!-- attribute porc_alumnos_mov_out_ano -->
			<?= $form->field($model, 'porc_alumnos_mov_out_ano')->textInput() ?>

<!-- attribute porc_alumnos_mov_out_gen -->
			<?= $form->field($model, 'porc_alumnos_mov_out_gen')->textInput() ?>

<!-- attribute porc_sexenios_vivos -->
			<?= $form->field($model, 'porc_sexenios_vivos')->textInput() ?>

<!-- attribute porc_prof_tiempo_completo -->
			<?= $form->field($model, 'porc_prof_tiempo_completo')->textInput() ?>

<!-- attribute porc_dir_tes_le_sexenios_vivos -->
			<?= $form->field($model, 'porc_dir_tes_le_sexenios_vivos')->textInput() ?>

<!-- attribute numero_publ_indexadas -->
			<?= $form->field($model, 'numero_publ_indexadas')->textInput() ?>

<!-- attribute numero_publ_no_indexadas -->
			<?= $form->field($model, 'numero_publ_no_indexadas')->textInput() ?>

<!-- attribute duracion_media_tiempo_completo -->
			<?= $form->field($model, 'duracion_media_tiempo_completo')->textInput() ?>

<!-- attribute duracion_media_tiempo_parcial -->
			<?= $form->field($model, 'duracion_media_tiempo_parcial')->textInput() ?>

<!-- attribute porc_abandono -->
			<?= $form->field($model, 'porc_abandono')->textInput() ?>

<!-- attribute porc_tesis_no_pri_prorroga -->
			<?= $form->field($model, 'porc_tesis_no_pri_prorroga')->textInput() ?>

<!-- attribute porc_tesis_no_seg_prorroga -->
			<?= $form->field($model, 'porc_tesis_no_seg_prorroga')->textInput() ?>

<!-- attribute porc_tesis_cum_laude -->
			<?= $form->field($model, 'porc_tesis_cum_laude')->textInput() ?>

<!-- attribute porc_tesis_men_internacional -->
			<?= $form->field($model, 'porc_tesis_men_internacional')->textInput() ?>

<!-- attribute porc_tesis_men_doc_industrial -->
			<?= $form->field($model, 'porc_tesis_men_doc_industrial')->textInput() ?>

<!-- attribute porc_tesis_cotutela -->
			<?= $form->field($model, 'porc_tesis_cotutela')->textInput() ?>

<!-- attribute num_medio_resultados_tesis -->
			<?= $form->field($model, 'num_medio_resultados_tesis')->textInput() ?>

<!-- attribute fecha_carga -->
			<?= $form->field($model, 'fecha_carga')->textInput() ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('models', 'Doctorado'),
    'content' => $this->blocks['main'],
    'active'  => true,
],
                    ]
                 ]
    );
    ?>
        <hr/>

        <?php echo $form->errorSummary($model); ?>

        <?= Html::submitButton(
        '<span class="glyphicon glyphicon-check"></span> ' .
        ($model->isNewRecord ? Yii::t('cruds', 'Create') : Yii::t('cruds', 'Save')),
        [
        'id' => 'save-' . $model->formName(),
        'class' => 'btn btn-success'
        ]
        );
        ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>

