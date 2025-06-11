<?php
/**
 * Fragmento de vista del formulario para añadir o editar el delegado de un agente.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

?>
<div class='agente-form'>

<?php
$form = ActiveForm::begin([
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n  {input}\n  {hint}\n  {error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-2',
            //'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-8',
            'error' => '',
            'hint' => '',
        ],
    ],
    'id' => 'Agente',  // formName del modelo
    'layout' => 'horizontal',
]);

echo "\n";

// Datos informativos para el usuario, no se tratan.
// attributes estudio_id, estudio_id_nk
echo Html::activeHiddenInput($model, 'estudio_id', ['value' => $plan->estudio->id_nk]) . "\n";
echo Html::activeHiddenInput($model, 'estudio_id_nk', ['value' => $plan->estudio->id_nk]) . "\n";
echo $form->field($plan, 'Estudio')->textInput(['value' => $plan->estudio->nombre, 'readonly' => 'readonly']) . "\n\n";

// attribute centro_id
echo Html::activeHiddenInput($model, 'centro_id', ['value' => $plan->centro_id]) . "\n";
echo $form->field($plan, 'Centro')
    ->textInput(['value' => $plan->centro->nombre, 'readonly' => true])
    ->label(Yii::t('cati', 'Centro')) . "\n\n";

// attribute plan_id_nk
echo $form->field($model, 'plan_id_nk')->textInput(['value' => $plan->id_nk, 'readonly' => true]) . "\n\n";

// Datos a tratar
// attribute nombre
echo $form->field($model, 'nombre')->textInput(['maxlength' => true]) . "\n\n";

// attribute apellido1
echo $form->field($model, 'apellido1')->textInput(['maxlength' => true]) . "\n\n";

// attribute apellido2
echo $form->field($model, 'apellido2')->textInput(['maxlength' => true]) . "\n\n";

// attribute nip
echo $form->field($model, 'nip')->textInput(['maxlength' => true]) . "\n\n";

// attribute email
echo $form->field($model, 'email')->input('email')
    ->hint(Yii::t('cati', 'Debe ser una dirección de la universidad.')) . "\n\n";

echo "<hr>\n";
echo $form->errorSummary($model) . "\n";
?>

<div class="form-group">
    <div class="col-lg-offset-2 col-lg-10" -->
        <?php
        echo Html::a(
            Yii::t('cati', 'Cancelar'),
            \yii\helpers\Url::previous(),
            ['class' => 'btn btn-default']
        ) . "&nbsp;\n&nbsp;";

        echo Html::submitButton(
            '<span class="glyphicon glyphicon-check"></span> ' . Yii::t('cruds', 'Save'),
            [
                'id' => 'save-' . $model->formName(),
                'class' => 'btn btn-success',
            ]
        ) . "\n"; ?>
    </div>
</div>
<?php
ActiveForm::end();
?>
</div>