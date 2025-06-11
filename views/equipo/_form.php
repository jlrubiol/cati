<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var app\models\Equipo $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="equipo-form">

    <?php $form = ActiveForm::begin([
    'id' => 'Equipo',
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
            

<!-- attribute estudio_id -->
			<?= $form->field($model, 'estudio_id')->textInput() ?>

<!-- attribute orden -->
			<?= $form->field($model, 'orden')->textInput() ?>

<!-- attribute estudio_id_nk -->
			<?= $form->field($model, 'estudio_id_nk')->textInput() ?>

<!-- attribute NIP -->
			<?= $form->field($model, 'NIP')->textInput() ?>

<!-- attribute nombre_equipo -->
			<?= $form->field($model, 'nombre_equipo')->textInput(['maxlength' => true]) ?>

<!-- attribute institucion -->
			<?= $form->field($model, 'institucion')->textInput(['maxlength' => true]) ?>

<!-- attribute nombre -->
			<?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

<!-- attribute apellido1 -->
			<?= $form->field($model, 'apellido1')->textInput(['maxlength' => true]) ?>

<!-- attribute apellido2 -->
			<?= $form->field($model, 'apellido2')->textInput(['maxlength' => true]) ?>

<!-- attribute URL_CV -->
			<?= $form->field($model, 'URL_CV')->textInput(['maxlength' => true]) ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('models', 'Equipo'),
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

