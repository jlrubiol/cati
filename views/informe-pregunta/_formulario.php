<?php
/**
 * Fragmento de vista del formulario para añadir o editar un apartado de los informes de autoevaluación.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin([
    'id' => 'informe-pregunta',
    'layout' => 'horizontal',
]);

echo "\n" . Html::activeHiddenInput($pregunta, 'id', ['value' => $pregunta->id]) . "\n";
if ($pregunta->isNewRecord) {
    echo "\n" . Html::activeHiddenInput($pregunta, 'tipo', ['value' => $tipo]) . "\n";
}

echo $form->field($pregunta, 'anyo')
    ->textInput([
        'value' => $pregunta->anyo,
        'placeholder' => Yii::t('cati', 'Año en que comienza el curso del informe.'),
        'readonly' => $pregunta->isNewRecord ? false : 'readonly',
    ])->hint('Ej: para el curso 2017-2018: 2017') . "\n";

echo $form->field($pregunta, 'apartado')
    ->textInput(['maxlength' => true])
    ->hint(Yii::t('cati', 'Ej: 1.3.2')) . "\n";

# Según el valor del campo `anyos_evaluacion` del estudio, el usuario cumplimentará IEC 1, IEC 3 o IEC 6.
echo $form->field($pregunta, 'editable')
    ->checkbox()
    ->hint(Yii::t('cati', 'Marque esta casilla si el usuario puede rellenar este apartado en <strong>algún</strong> IEC.')) . "\n";

echo $form->field($pregunta, 'editable_1')
    ->checkbox()
    ->hint(Yii::t('cati', 'Marque esta casilla si el usuario puede rellenar este apartado en los IEC 1.<br><strong>Requiere</strong> haber marcado «Editable».')) . "\n";

echo $form->field($pregunta, 'invisible_1')
    ->checkbox()
    ->hint(Yii::t('cati', 'Marque esta casilla si este apartado <strong>no</strong> se debe mostrar en los IEC 1.')) . "\n";

echo $form->field($pregunta, 'invisible_3')
    ->checkbox()
    ->hint(Yii::t('cati', 'Marque esta casilla si este apartado <strong>no</strong> se debe mostrar en los IEC 3.')) . "\n";

/*
echo $form->field($pregunta, 'tabla')
    ->textInput(['maxlength' => true])
    ->hint(Yii::t('cati', 'En su caso, el nombre de la tabla a incluir en este apartado.'))."\n";
*/

echo $form->field($pregunta, 'titulo')
    ->textArea(['maxlength' => true, 'placeholder' => Yii::t('cati', 'Título del apartado')]) . "\n";

echo $form->field($pregunta, 'info')
    ->textArea(['maxlength' => true, 'placeholder' => Yii::t('cati', 'Dirección web con información sobre cómo rellenar este apartado.')]) . "\n";

echo $form->field($pregunta, 'explicacion')
    ->textArea([
        'placeholder' => Yii::t('cati', 'Si lo desea, puede introducir una explicación.'),
        'rows' => 12,
    ]) . "\n";

echo $form->field($pregunta, 'texto_comun')
    ->textArea([
        'placeholder' => Yii::t('cati', 'En su caso, texto común introducido por la VG para todos los estudios.'),
        'rows' => 12,
    ]) . "\n";
?>

<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?= Html::submitButton(
            '<span class="glyphicon glyphicon-check"></span> ' . ($pregunta->isNewRecord ? Yii::t('cruds', 'Create')
                                                                                         : Yii::t('cruds', 'Save')),
            [
                'id' => 'save-pregunta',
                'class' => 'btn btn-success',
            ]
        ); ?>
    </div>
</div>
<?php
ActiveForm::end();
