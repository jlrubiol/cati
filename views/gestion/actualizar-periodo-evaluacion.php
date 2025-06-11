<?php

use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Actualizar duración del periodo de evaluación del estudio');

$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Periodos de evaluación de los estudios'). ' ' . $estudio->anyo_academico . '/' . ($estudio->anyo_academico + 1),
    'url' => ['gestion/ver-periodos-evaluacion', 'anyo' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['gestion/ver-periodo-evaluacion', 'id' => $estudio->id],
];
$this->params['breadcrumbs'][] = Yii::t('gestion', 'Actualizar');

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
$form = ActiveForm::begin([
    'action' => Url::to(['//gestion/guardar-periodo-evaluacion']),
    'id' => 'periodo-evaluacion',
    'layout' => 'horizontal',
]);

echo $form->field($estudio, 'id')->hiddenInput(['value' => $estudio->id])->label(false);
echo $form->field($estudio, 'id_nk')->textInput(['value' => $estudio->id_nk, 'readonly' => 'readonly']);
echo $form->field($estudio, 'nombre')->textInput(['value' => $estudio->nombre, 'readonly' => 'readonly']);

echo $form->field($estudio, 'anyos_evaluacion')->textInput(['type' => 'number']);
?>
<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?php echo Html::submitButton(
            '<span class="glyphicon glyphicon-check"></span> ' . Yii::t('cruds', 'Save'),
            ['id' => 'actualizar-periodo-evaluacion', 'class' => 'btn btn-success']
        ); ?>
    </div>
</div>
<?php
ActiveForm::end();
