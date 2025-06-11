<?php
/**
 * Fragmento de vista del formulario para añadir o editar un registro de los planes de innovación y mejora.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/*
$campos = ['apartado_memoria', 'titulo', 'descripcion_breve', 'descripcion_amplia', 'responsable_accion',
    'problema', 'objetivo', 'acciones',
    'inicio', 'final', 'responsable_competente', 'justificacion', 'nivel', 'fecha',
    'plazo_implantacion', 'indicador', 'meta', 'valor', 'cumplimiento',
];
*/
$explicaciones = [
    'responsable_accion' => Yii::t(
        'cati',
        'Responsable que impulsa la propuesta:'
        . ' Coordinador Titulación, miembros C.G.C., miembro equipo Decanal, etc., persona física.'
    ),
    'responsable_competente' => Yii::t(
        'cati',
        'Persona o equipo competentes en la'
        . ' materia, según el Procedimiento de que se trate: Departamento, Decanato, Gerencia, Vicerrectorado.'
    ),
    'nivel' => Yii::t('cati', 'Nivel propuesto: 1, 2 ó 3.'),  // Yii::t('cati', 'Nivel propuesto: 1, 2, 3 ó 4.'),
    # 'objetivo' => Yii::t('doct', 'Expresar en forma de indicador para el seguimiento cuando sea posible.'),
    'objetivo' => Yii::t('cati', 'QUÉ quiero conseguir. Puede alinearse con un objetivo de la titulación o del centro.'),
    'acciones' => Yii::t(
        'doct',
        'Coordinador (C), Comisión Académica (CA), Escuela de Doctorado (ED), Universidad de Zaragoza (UZ)'
    ),
    # 'indicador' => Yii::t('cati', 'Tiene por objeto proporcionar información sobre los parámetros ligados a la acción de mejora.'),
    'indicador' => Yii::t('cati', 'En caso de incluir más de un indicador deberá numerarlos hasta un máximo de tres. Ejemplo: IND-1: Nº de charlas; IND-2: Tasa de respuestas; IND-3: Elaboración de informe sí/no.'),
    'valores_a_alcanzar' => Yii::t('cati', 'Deberá indicar un valor a alcanzar por cada indicador definido. Ejemplo: IND-1: 4; IND-2: 40%; IND-3: Sí.'),
    'necesidad_detectada' => Yii::t('cati', 'Hecho o circunstancia que requiere una mejora.'),
    'descripcion_breve' => Yii::t('cati', 'CÓMO lo voy a conseguir. Acción concreta a desarrollar. Debe ser realista. Si modifica MV, introducir la modificación concreta a realizar.'),
    'responsable_aprobacion_id' => Yii::t('cati', 'El responsable de aprobar la acción no tiene por qué coincidir con quien la ejecuta.'),
    'plazo_id' => Yii::t('cati', 'Curso en el que se va a implementar la acción.'),
    'observaciones' => Yii::t('cati', 'Evidencias o valoraciones acerca del grado de cumplimiento de la acción'),
    'valores_alcanzados' => Yii::t('cati', 'Deberá indicar el valor alcanzado por cada indicador definido, en el mismo formato inicial. Ejemplo: IND-1: 3; IND-2: 45%; IND-3: Sí'),
    'estado_id' => Yii::t('cati', 'Ejecutada: acción implementada en su totalidad.<br>
        En curso: acción puesta en marcha, pero sin finalizar.<br>
        Pendiente: acción sin iniciar, que sí se va a ejecutar.<br>
        Desestimada: acción sin iniciar, que no se va a ejecutar.')
];

function mostrarAtributo($pregunta, $campo)
{
    return false !== strpos($pregunta->atributos, $campo);
}

echo '<h2>' . HtmlPurifier::process($pregunta->titulo) . "</h2>\n";
echo '<p>' . HtmlPurifier::process($pregunta->explicacion) . "</p><br>\n\n";

$form = ActiveForm::begin(
    [
        'enableClientValidation' => true,
        'errorSummaryCssClass' => 'error-summary alert alert-danger',
        'id' => 'PlanRespuesta',  // formName del modelo
        'layout' => 'horizontal',
    ]
);

echo "\n";
echo Html::activeHiddenInput($respuesta, 'estudio_id') . "\n";
echo Html::activeHiddenInput($respuesta, 'estudio_id_nk', ['value' => $estudio->id_nk]) . "\n";
echo Html::activeHiddenInput($respuesta, 'anyo', ['value' => $pregunta->anyo]) . "\n";
echo Html::activeHiddenInput($respuesta, 'plan_pregunta_id') . "\n";
echo Html::activeHiddenInput($respuesta, 'apartado', ['value' => $pregunta->apartado]) . "\n";
echo Html::activeHiddenInput($respuesta, 'language') . "\n";

echo $form->field($respuesta, 'estudio')->textInput(['value' => $estudio->nombre, 'readonly' => 'readonly']);
echo $form->field($respuesta, 'curso')->label('Campaña de elaboración')->textInput(
    [
        'value' => ($pregunta->anyo + 1),
        'readonly' => 'readonly',
    ]
);

$campos_pregunta = array_map(function ($a) { return trim($a); }, explode(',', $pregunta->atributos));

foreach ($campos_pregunta as $campo) {
    try {
        $tipo_dato = \app\models\PlanRespuestaLang::getTableSchema()->columns[$campo]->dbType;
    } catch (Exception $e) {
        $tipo_dato = \app\models\PlanRespuesta::getTableSchema()->columns[$campo]->dbType;
    }
    preg_match('/\((.*?)\)/', $tipo_dato, $matches);
    $longitud_dato = $matches ? (int)$matches[1] : 0;

    if (in_array($campo, ['ambito_id', 'responsable_aprobacion_id', 'plazo_id', 'apartado_memoria_id', 'tipo_modificacion_id', 'seguimiento_id'])) {
        echo $form->field($respuesta, $campo)
            ->dropDownList(
                \yii\helpers\ArrayHelper::map(
                    app\models\PaimOpcion::find()->where(
                        ['anyo' => $pregunta->anyo, 'campo' => $campo, 'tipo_estudio' => $estudio->getTipoEstudio()]
                        )->orderBy('valor')->all(),
                    'id',
                    'valor'
                ),
                [
                    'prompt' => Yii::t('cruds', 'Select'),
                    'disabled' => true,  # (isset($relAttributes) && isset($relAttributes[$campo])),
                ]
            )
            ->hint(isset($explicaciones[$campo]) ? $explicaciones[$campo] : '') . "\n";
        ;

        continue;
    }

    echo $form->field($respuesta, $campo)
        ->textarea(['maxlength' => true, 'rows' => ($tipo_dato === 'text') ? 15 : (($longitud_dato > 80) ? 2 : 1), 'readonly' => 'readonly'])
        ->hint(isset($explicaciones[$campo]) ? $explicaciones[$campo] : '') . "\n"
    ;
}

$campos_a_completar = [];
if (in_array('tipo_modificacion_id', $campos_pregunta)) {
    $campos_a_completar = ['estado_id', 'observaciones'];
} else {
    $campos_a_completar = ['valores_alcanzados', 'estado_id', 'observaciones'];
}

foreach ($campos_a_completar as $campo) {
    try {
        $tipo_dato = \app\models\PlanRespuestaLang::getTableSchema()->columns[$campo]->dbType;
    } catch (Exception $e) {
        $tipo_dato = \app\models\PlanRespuesta::getTableSchema()->columns[$campo]->dbType;
    }
    preg_match('/\((.*?)\)/', $tipo_dato, $matches);
    $longitud_dato = $matches ? (int)$matches[1] : 0;

    if ($campo == 'estado_id') {
        echo $form->field($respuesta, $campo)
            ->dropDownList(
                \yii\helpers\ArrayHelper::map(
                    app\models\PaimOpcion::find()->where(['anyo' => $pregunta->anyo, 'campo' => $campo, 'tipo_estudio' => $estudio->getTipoEstudio()])->all(),
                    'id',
                    'valor'
                ),
                [
                    'prompt' => Yii::t('cruds', 'Select'),
                    'disabled' => (isset($relAttributes) && isset($relAttributes[$campo])),
                    'required' => true,
                ]
            )
            ->hint(isset($explicaciones[$campo]) ? $explicaciones[$campo] : '') . "\n";
        ;
    } else {
        echo $form->field($respuesta, $campo)
            ->textarea([
                'maxlength' => true,
                'rows' => ($tipo_dato === 'text') ? 15 : (($longitud_dato > 80) ? 2 : 1),
                'required' => ($campo != 'observaciones'),
            ])
            ->hint(isset($explicaciones[$campo]) ? $explicaciones[$campo] : '') . "\n"
        ;
    }
}

?>
<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?php echo Html::submitButton(
            '<span class="glyphicon glyphicon-check"></span> ' . Yii::t('cruds', 'Save'),
            [
                'id' => 'guardar-registro',
                'class' => 'btn btn-success',
            ]
        ); ?>
    </div>
</div>
<?php
ActiveForm::end();
