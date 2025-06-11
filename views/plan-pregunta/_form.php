<?php
/**
 * /home/quique/Devel/cati/cati/runtime/giiant/4b7e79a8340461fe629a6ac612644d03
 *
 * @package default
 */


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
 *
 * @var yii\web\View $this
 * @var app\models\PlanPregunta $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="plan-pregunta-form">

    <?php $form = ActiveForm::begin([
		'id' => 'PlanPregunta',
		'layout' => 'horizontal',
		'enableClientValidation' => true,
		'errorSummaryCssClass' => 'error-summary alert alert-danger'
	]
);
?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>


<!-- attribute anyo -->
			<?php echo $form->field($model, 'anyo')->textInput() ?>

<!-- attribute apartado -->
			<?php echo $form->field($model, 'apartado')->textInput(['maxlength' => true]) ?>

<!-- attribute tipo -->
			<?php echo $form->field($model, 'tipo')->textInput(['maxlength' => true]) ?>

<!-- attribute atributos -->
			<?php echo $form->field($model, 'atributos')->textInput(['maxlength' => true]) ?>

<!-- attribute titulo -->

<!-- attribute explicacion -->
        </p>
        <?php $this->endBlock(); ?>

        <?php echo
Tabs::widget(
	[
		'encodeLabels' => false,
		'items' => [
			[
				'label'   => Yii::t('models', 'PlanPregunta'),
				'content' => $this->blocks['main'],
				'active'  => true,
			],
		]
	]
);
?>
        <hr/>

        <?php echo $form->errorSummary($model); ?>

        <?php echo Html::submitButton(
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
