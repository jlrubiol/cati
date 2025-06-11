<?php

use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Actualizar dirección');
if (Yii::$app->user->can('doctorado')) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/doctorado']];
    $this->params['breadcrumbs'][] = [
        'label' => Yii::t('gestion', 'Webs específicas'),
        'url' => ['gestion/ver-webs-especificas'],
    ];
    $this->params['breadcrumbs'][] = [
        'label' => Yii::t('models', 'Dirección de la web específica del plan'),
        'url' => ['gestion/ver-url-web-plan', 'id' => $plan->id],
    ];
} else {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/index']];
    $this->params['breadcrumbs'][] = [
        'label' => Yii::t('models', 'Dirección de la web específica del plan'),
        'url' => ['gestion/ver-url-web-plan', 'id' => $plan->id],
    ];
}
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
$form = ActiveForm::begin([
    'action' => Url::to(['//gestion/guardar-url-web-plan']),
    'id' => 'url-web-plan',
    'layout' => 'horizontal',
]);

echo Html::activeHiddenInput($plan, 'id', ['value' => $plan->id]);
echo $form->field($plan, 'estudio_id_nk')->textInput(['value' => $plan->estudio_id_nk, 'readonly' => 'readonly']);
echo $form->field($plan, 'id_nk')->textInput(['value' => $plan->id_nk, 'readonly' => 'readonly']);
echo $form->field($plan, 'anyo_academico')->textInput(['value' => $plan->anyo_academico, 'readonly' => 'readonly']);
echo $form->field($plan, 'Estudio')->textInput(['value' => $plan->estudio->nombre, 'readonly' => 'readonly']);
echo $form->field($plan, 'Centro')->textInput(['value' => $plan->centro->nombre, 'readonly' => true]);
echo $form->field($plan, 'url_web_plan')->textInput(['maxlength' => true]);
?>
<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?php echo Html::submitButton(
            '<span class="glyphicon glyphicon-check"></span> ' . Yii::t('cruds', 'Save'),
            ['id' => 'actualizar-url-web-plan', 'class' => 'btn btn-success']
        ); ?>
    </div>
</div>
<?php
ActiveForm::end();
