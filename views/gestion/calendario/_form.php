<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\StringHelper;
use app\models\Calendario;

/**
 * @var yii\web\View $this
 * @var app\models\Calendario $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="calendario-form">

    <?php
    $form = ActiveForm::begin(
        [
            'id' => 'Calendario',
            'layout' => 'horizontal',  // 'default',
            'enableClientValidation' => true,
            'errorSummaryCssClass' => 'error-summary alert alert-danger',
            'fieldConfig' => [
                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-2',
                    // 'offset' => 'col-sm-offset-4',
                    'wrapper' => 'col-sm-8',
                    'error' => '',
                    'hint' => '',
                ],
            ],
        ]
    );

    // attribute evento
    // echo $form->field($model, 'evento')->textInput(['maxlength' => true]) . "\n";
    echo $form->field($model, 'evento')->dropDownList(Calendario::EVENTOS, ['prompt' => Yii::t('cati', 'Seleccione un evento')]);

    // attribute anyo
    echo $form->field($model, 'anyo')->textInput() . "\n";

    // attribute fecha.  También se podría usar https://github.com/2amigos/yii2-date-picker-widget
    echo $form->field($model, 'fecha')->widget(\yii\jui\DatePicker::class, [
        'clientOptions' => [
            'buttonText' => "<span class='glyphicon glyphicon-calendar'></span>",
            'changeMonth' => true,
            'changeYear' => true,
            'showAnim' => 'slide',
            'showOn'=> 'both',  // 'button',
            'yearRange' => 'c-1:c+3',
        ],
        'dateFormat' => 'yyyy-MM-dd',
        // 'options' => ['class' => 'form-control'],
    ]) . "\n";
    ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-8">
            <?php echo Html::submitButton(
                '<span class="glyphicon glyphicon-check"></span> ' .
                ($model->isNewRecord ? Yii::t('cruds', 'Create') : Yii::t('cruds', 'Save')),
                [
                    'id' => 'save-' . $model->formName(),
                    'class' => 'btn btn-success'
                ]
            );
            ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>