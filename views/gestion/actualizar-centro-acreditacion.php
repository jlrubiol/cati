<?php

use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Actualizar fecha y web de la acreditación');

$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Acreditación institucional de los centros'),
    'url' => ['gestion/ver-centros-acreditacion'],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('models', 'Acreditación del centro'),
    'url' => ['gestion/ver-centro-acreditacion', 'id' => $centro->id],
];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
$form = ActiveForm::begin([
    'action' => Url::to(['//gestion/guardar-centro-acreditacion']),
    'id' => 'centro-acreditacion',
    'layout' => 'horizontal',
]);

echo Html::activeHiddenInput($centro, 'id', ['value' => $centro->id]);
echo $form->field($centro, 'nombre')->textInput(['value' => $centro->nombre, 'readonly' => 'readonly']);
echo $form->field($centro, 'direccion')->textInput(['value' => $centro->direccion, 'readonly' => 'readonly']);
echo $form->field($centro, 'municipio')->textInput(['value' => $centro->municipio, 'readonly' => 'readonly']);
echo $form->field($centro, 'telefono')->textInput(['value' => $centro->telefono, 'readonly' => 'readonly']);
echo $form->field($centro, 'url')->textInput(['value' => $centro->url, 'readonly' => 'readonly']);
echo $form->field($centro, 'nombre_decano')->textInput(['value' => $centro->nombre_decano, 'readonly' => true]);
echo $form->field($centro, 'email_decano')->textInput(['value' => $centro->email_decano, 'readonly' => true]);
echo $form->field($centro, 'acreditacion_url')->textInput(['maxlength' => true]);
// attribute fecha_acreditacion.  También se podría usar <https://github.com/2amigos/yii2-date-picker-widget>
echo $form->field($centro, 'fecha_acreditacion')->widget(\yii\jui\DatePicker::class, [
    'clientOptions' => [
        'buttonText' => "<span class='glyphicon glyphicon-calendar'></span>",
        'changeMonth' => true,
        'changeYear' => true,
        'showAnim' => 'slide',
        'showOn'=> 'both',  // 'button',
        'yearRange' => 'c-5:c',
    ],
    'dateFormat' => 'yyyy-MM-dd',
    // 'options' => ['class' => 'form-control'],
]) . "\n";
echo $form->field($centro, 'anyos_validez')->textInput(['type' => 'number']);
?>
<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?php echo Html::submitButton(
            '<span class="glyphicon glyphicon-check"></span> ' . Yii::t('cruds', 'Save'),
            ['id' => 'actualizar-centro-acreditacion', 'class' => 'btn btn-success']
        ); ?>
    </div>
</div>
<?php
ActiveForm::end();
