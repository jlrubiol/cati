<?php

use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Actualizar horario');
if (Yii::$app->user->can('gradoMaster')) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/grado-master']];
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Horarios'), 'url' => ['//gestion/ver-horarios']];
} else {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/index']];
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Horario'), 'url' => ['//gestion/ver-horario', 'id' => $plan->id]];
}
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
$form = ActiveForm::begin([
    'action' => Url::to(['//gestion/guardar-horario']),
    'id' => 'horario',
    'layout' => 'horizontal',
]);

echo Html::activeHiddenInput($plan, 'id', ['value' => $plan->id]);
echo $form->field($plan, 'Estudio')->textInput(['value' => $plan->estudio->nombre, 'readonly' => 'readonly']);
echo $form->field($plan, 'Centro')->textInput(['value' => $plan->centro->nombre, 'readonly' => true]);
echo $form->field($plan, 'url_horarios')->textInput(['maxlength' => true]);
?>
<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?php echo Html::submitButton(
            '<span class="glyphicon glyphicon-check"></span> ' . Yii::t('cruds', 'Save'),
            [
                'id' => 'actualizar-horario',
                'class' => 'btn btn-success',
            ]
        ); ?>
    </div>
</div>
<?php
ActiveForm::end();
