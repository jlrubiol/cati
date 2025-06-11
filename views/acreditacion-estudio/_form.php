<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var app\models\AcreditacionEstudio $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="acreditacion-estudio-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AcreditacionEstudio',
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
            

            <!-- attribute nk -->
			<?= $form->field($model, 'nk')->textInput() ?>

            <!-- attribute cod_ruct -->
			<?= $form->field($model, 'cod_ruct')->textInput() ?>

            <!-- attribute fecha_verificacion -->
            <?= $form->field($model, 'fecha_verificacion')->textInput() ?>

            <!-- attribute fecha_implantacion -->
			<?= $form->field($model, 'fecha_implantacion')->textInput() ?>

            <!-- attribute fecha_acreditacion -->
			<?= $form->field($model, 'fecha_acreditacion')->textInput() ?>

            <!-- attribute anyos_validez -->
            <?= $form->field($model, 'anyos_validez')->textInput() ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('models', 'AcreditacionEstudio'),
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

