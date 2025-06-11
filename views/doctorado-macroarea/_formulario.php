<?php
/**
 * Fragmento de vista del formulario para editar los datos por macroárea de los PD.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

$rama = app\models\Rama::findOne(['id' => $model->cod_rama_conocimiento]);

?>
<div class='doctorado-macroarea-form'>

<?php
$form = ActiveForm::begin([
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n  {input}\n  {hint}\n  {error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-8',
            //'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-2',
            'error' => '',
            'hint' => '',
        ],
    ],
    'id' => 'DoctoradoMacroarea',  // formName del modelo
    'layout' => 'horizontal',
]);

echo "\n";

// Datos informativos para el usuario, no se tratan.
// attribute ano_academico
echo $form->field(
    $model,
    'ano_academico2',
    [
        'inputOptions' => ['value' => sprintf('%d/%d', $model->ano_academico, $model->ano_academico + 1)],
        'enableClientValidation' => false
    ]
)->textInput(['readonly' => 'readonly'])->label("Año académico") . "\n\n";

echo $form->field(
    $model,
    'cod_rama_conocimiento2',
    [
        'inputOptions' => ['value' => $model->cod_rama_conocimiento === '*' ? Yii::t('doct', 'Todas') : $rama->nombre],
        'enableClientValidation' => false
    ]
)->textInput(['readonly' => 'readonly'])->label("Macroárea") . "\n\n";


// DATOS A TRATAR. Los campos están definidos en el modelo `DoctoradoMacroarea` (tabla `DATUZ_doctorado_macroarea`).
// 1.9.b. Porcentaje de estudiantes con beca distinta de las contempladas en indicador 1.9
echo $form->field($model, 'porc_alumnos_beca_distinta')->textInput(['maxlength' => true]) . "\n\n";

// 2.3.1. Numerador: Número de estudiantes que han realizado actividades transversales
echo $form->field($model, 'alumnos_act_transv')->textInput(['maxlength' => true]) . "\n\n";

// 2.3.2. Actividades transversales de la EDUZ: cursos
echo $form->field($model, 'cursos_act_transv')->textInput(['maxlength' => true]) . "\n\n";

// 3.1. Porcentaje de estudiantes del programa de doctorado que han realizado estancias de investigación en el año
echo $form->field($model, 'porc_alumnos_mov_out_ano')->textInput(['maxlength' => true]) . "\n\n";

# El indicador 3.2 deja de mostrarse porque no aporta información relevante y no merece la pena el esfuerzo de rellenarlo manual (decisión MARU en reunión de 26 de abril de 2024).
// 3.2. Porcentaje de estudiantes del programa de doctorado que han realizado estancias de investigación
// echo $form->field($model, 'porc_alumnos_mov_out_gen')->textInput(['maxlength' => true]) . "\n\n";

// 4.5. Numerador: Número de miembros internacionales en los tribunales
echo $form->field($model, 'numero_expertos_int_trib')->textInput(['maxlength' => true]) . "\n\n";

// 4.5. Denominador: Número de miembros tribunales de tesis defendidas en el curso objeto del estudio
echo $form->field($model, 'numero_miembros_trib')->textInput(['maxlength' => true]) . "\n\n";

// 6.11. Número medio de resultados científicos de las tesis doctorales
echo $form->field($model, 'num_medio_resultados_tesis')->textInput(['maxlength' => true]) . "\n\n";

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