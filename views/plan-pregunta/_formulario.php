<?php
/**
 * Fragmento de vista del formulario para añadir o editar un apartado de los planes de innovación y mejora.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin([
    'id' => 'plan-pregunta',
    'layout' => 'horizontal',
]);

echo "\n" . Html::activeHiddenInput($pregunta, 'id', ['value' => $pregunta->id]) . "\n";
if ($pregunta->isNewRecord) {
    echo "\n" . Html::activeHiddenInput($pregunta, 'tipo', ['value' => $tipo]) . "\n";
}

echo $form->field($pregunta, 'anyo')
    ->textInput([
        'value' => $pregunta->anyo,
        'placeholder' => Yii::t('cati', 'Año en que comienza el curso del plan de innovación y mejora.'),
        'readonly' => $pregunta->isNewRecord ? false : 'readonly',
    ])->hint('Ej: para el curso 2017-2018: 2017') . "\n";

echo $form->field($pregunta, 'apartado')
    ->textInput(['maxlength' => true])
    ->hint(Yii::t('cati', 'Ej: 1.2')) . "\n";

echo $form->field($pregunta, 'titulo')
    ->textArea(['maxlength' => true, 'placeholder' => Yii::t('cati', 'Título del apartado')]) . "\n";

echo $form->field($pregunta, 'explicacion')
    ->textArea(['placeholder' => Yii::t('cati', 'Si lo desea, puede introducir una explicación.')]) . "\n";

echo $form->field($pregunta, 'atributos')
    ->textArea(['placeholder' => Yii::t('cati', 'Listado separado por comas de los atributos del apartado')])
    ->hint(Yii::t('cati', 'Atributos posibles: apartado_memoria, titulo, descripcion_breve, descripcion_amplia,'
      . ' problema, objetivo, acciones,'
      . ' justificacion_breve, justificacion, nivel, responsable_accion, inicio, final, responsable_competente, fecha,'
      . ' plazo_implantacion, indicador, valores_a_alcanzar, valores_alcanzados, cumplimiento,'
      . ' necesidad_detectada, observaciones')) . "\n";
?>

<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?php echo Html::submitButton(
            '<span class="glyphicon glyphicon-check"></span> '
              . ($pregunta->isNewRecord ? Yii::t('cruds', 'Create') : Yii::t('cruds', 'Save')),
            [
                'id' => 'save-pregunta',
                'class' => 'btn btn-success',
            ]
        ); ?>
    </div>
</div>
<?php
ActiveForm::end();
