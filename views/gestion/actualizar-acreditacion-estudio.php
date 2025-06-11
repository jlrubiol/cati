<?php

use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Actualizar datos de acreditación del estudio');

$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Acreditación de los estudios'),
    'url' => ['gestion/ver-acreditacion-estudios'],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('models', 'Acreditación del estudio'),
    'url' => ['gestion/ver-acreditacion-estudio', 'nk' => $acreditacion->nk],
];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
$form = ActiveForm::begin([
    'action' => Url::to(['//gestion/guardar-acreditacion-estudio']),
    'id' => 'acreditacion-estudio',
    'layout' => 'horizontal',
]);

echo $form->field($acreditacion, 'nk')->textInput(['value' => $acreditacion->nk, 'readonly' => 'readonly']);
echo $form->field($acreditacion, 'nombre')->textInput(['value' => $acreditacion->estudio->nombre, 'readonly' => 'readonly']);
echo $form->field($acreditacion, 'cod_ruct')->textInput(['value' => $acreditacion->cod_ruct]);

echo $form->field($acreditacion, 'esInteruniversitario')->textInput(['value' => $acreditacion->esInteruniversitario ? 'Sí' : 'No', 'readonly' => true]);
echo $form->field($acreditacion, 'coordinaUz')->textInput(['value' => $acreditacion->esInteruniversitario ? ($acreditacion->coordinaUz ? 'Sí' : 'No') : null, 'readonly' => true]);

// attribute fecha_verificacion.  También se podría usar <https://github.com/2amigos/yii2-date-picker-widget>
echo $form->field($acreditacion, 'fecha_verificacion')->widget(\yii\jui\DatePicker::class, [
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

// attribute fecha_implantacion.  También se podría usar <https://github.com/2amigos/yii2-date-picker-widget>
echo $form->field($acreditacion, 'fecha_implantacion')->widget(\yii\jui\DatePicker::class, [
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

// attribute fecha_acreditacion.  También se podría usar <https://github.com/2amigos/yii2-date-picker-widget>
echo $form->field($acreditacion, 'fecha_acreditacion')->widget(\yii\jui\DatePicker::class, [
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

echo $form->field($acreditacion, 'anyos_validez')->textInput(['type' => 'number']);
?>
<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?php
        echo Html::a(
            '<span class="glyphicon glyphicon-remove"></span> ' . Yii::t('cati', 'Cancelar'),
            ['//gestion/ver-acreditacion-estudios'],
            ['class' => 'btn btn-default']
        ) . "&nbsp;\n&nbsp;";

        echo Html::submitButton(
            '<span class="glyphicon glyphicon-check"></span> ' . Yii::t('cruds', 'Save'),
            ['id' => 'actualizar-acreditacion-estudio', 'class' => 'btn btn-success']
        ); ?>
    </div>
</div>
<?php
ActiveForm::end();
