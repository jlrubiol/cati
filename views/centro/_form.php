<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var app\models\Centro $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="centro-form">

    <?php $form = ActiveForm::begin([
    'id' => 'Centro',
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
            

<!-- attribute activo -->
			<?= $form->field($model, 'activo')->textInput() ?>

<!-- attribute nip_decano -->
			<?= $form->field($model, 'nip_decano')->textInput() ?>

<!-- attribute anyos_validez -->
			<?= $form->field($model, 'anyos_validez')->textInput() ?>

<!-- attribute fecha_acreditacion -->
			<?= $form->field($model, 'fecha_acreditacion')->textInput() ?>

<!-- attribute rrhh_id_nk -->
			<?= $form->field($model, 'rrhh_id_nk')->textInput(['maxlength' => true]) ?>

<!-- attribute tipo_centro -->
			<?= $form->field($model, 'tipo_centro')->textInput(['maxlength' => true]) ?>

<!-- attribute telefono -->
			<?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?>

<!-- attribute direccion -->
			<?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?>

<!-- attribute municipio -->
			<?= $form->field($model, 'municipio')->textInput(['maxlength' => true]) ?>

<!-- attribute url -->
			<?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

<!-- attribute nombre_decano -->
			<?= $form->field($model, 'nombre_decano')->textInput(['maxlength' => true]) ?>

<!-- attribute email_decano -->
			<?= $form->field($model, 'email_decano')->textInput(['maxlength' => true]) ?>

<!-- attribute tratamiento_decano -->
			<?= $form->field($model, 'tratamiento_decano')->textInput(['maxlength' => true]) ?>

<!-- attribute acreditacion_url -->
			<?= $form->field($model, 'acreditacion_url')->textInput(['maxlength' => true]) ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('models', 'Centro'),
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

