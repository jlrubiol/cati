<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var app\models\InformePregunta $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="informe-pregunta-form">

    <?php $form = ActiveForm::begin([
    'id' => 'InformePregunta',
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
            

<!-- attribute anyo -->
			<?= $form->field($model, 'anyo')->textInput() ?>

<!-- attribute editable -->
			<?= $form->field($model, 'editable')->textInput() ?>

<!-- attribute editable_1 -->
			<?= $form->field($model, 'editable_1')->textInput() ?>

<!-- attribute invisible_1 -->
			<?= $form->field($model, 'invisible_1')->textInput() ?>

<!-- attribute invisible_3 -->
			<?= $form->field($model, 'invisible_3')->textInput() ?>

<!-- attribute oblig_def -->
			<?= $form->field($model, 'oblig_def')->textInput() ?>

<!-- attribute apartado -->
			<?= $form->field($model, 'apartado')->textInput(['maxlength' => true]) ?>

<!-- attribute tipo -->
			<?= $form->field($model, 'tipo')->textInput(['maxlength' => true]) ?>

<!-- attribute tabla -->
			<?= $form->field($model, 'tabla')->textInput(['maxlength' => true]) ?>

<!-- attribute titulo -->

<!-- attribute info -->

<!-- attribute explicacion -->

<!-- attribute texto_comun -->
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('models', 'InformePregunta'),
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

