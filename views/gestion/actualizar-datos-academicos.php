<?php

use yii\helpers\Html;

$this->title = Yii::t('gestion', 'Actualizar datos académicos');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
/*
DATUZ_nuevo_ingreso             | NuevoIngreso             | $nuevos_ingresos $dpNuevosIngresos $edades $estudiosPrevios $generos $notasMedias $procedencias
DATUZ_estudio_previo_master     | EstudioPrevioMaster      | $dpsEstudiosPrevios
DATUZ_asignatura_calificacion   | AsignaturaCalificacion   | $dpsCalificaciones
DATUZ_asignatura_indicador      | AsignaturaIndicador      | $indicadores
DATUZ_acreditacion_titulaciones | AcreditacionTitulaciones | $dpMovilidades $globales $globales_definitivos $globales_abandono
*/
echo yii\bootstrap\Alert::widget(
    [
        'body' => "<span class='glyphicon glyphicon-info-sign'></span>"
          . Yii::t(
            'gestion',
            "<p>Desde esta página puede <strong>importar</strong> desde DATUZ
            los <b>datos académicos</b> de Grado y Máster de un curso académico.</p>

            <p>Los datos académicos se muestran en la web de estudios, así como en los IEC.</p>

            <p>Para el curso indicado se actualizarán concretamente los datos de:</p>

            <ul>
                <li>Oferta / Nuevo ingreso / Matrícula</li>
                <li>Créditos reconocidos</li>
                <li>Estudio previo de los estudiantes de nuevo ingreso</li>
                <li>Perfil de ingreso de los estudiantes: procedencia</li>
                <li>Perfil de ingreso de los estudiantes: género</li>
                <li>Perfil de ingreso de los estudiantes: edad</li>
                <li>Nota media de admisión y nota de corte</li>

                <li>Distribución de calificaciones</li>
                <li>Análisis de los indicadores del título</li>
                <li>Tasas de éxito/rendimiento/eficiencia</li>
                <li>Tasas de abandono/graduación</li>
                <li>Tasas de duración</li>

                <!--
                <li>Cursos de adaptación al grado</li>
                <li>Estudiantes en planes de movilidad</li>
                -->
            </ul>"
        ),
        'options' => ['class' => 'alert-info'],
    ]
) . "\n\n";

echo Html::beginForm('', 'post', ['class' => 'form-horizontal']) . "\n\n";

echo Html::beginTag('div', ['class' => 'form-group']) . "\n";
echo Html::label(Yii::t('app', 'Año de inicio del curso'), 'curso', ['class' => 'control-label col-sm-3']) . "\n";
echo Html::beginTag('div', ['class' => 'col-sm-6']) . "\n";
echo Html::textInput('curso', date('Y') - 1, ['class' => 'form-control', 'placeholder' => Yii::t('gestion', 'Año en el que comienza el curso')]) . "\n";
echo Html::tag('p', nl2br(Yii::t('gestion', "Introduzca el año en que se inicia el curso académico cuyos datos desea actualizar.\nPor ejemplo, para el curso 2018-2019, introduzca «2018».")), ['class' => 'help-block']) . "\n";
echo Html::endTag('div') . "\n";
echo Html::endTag('div') . "\n\n";

echo Html::beginTag('div', ['class' => 'form-group']) . "\n";
echo Html::beginTag('div', ['class' => 'col-lg-offset-3 col-lg-9']) . "\n";
echo Html::submitButton(Yii::t('gestion', 'Actualizar'), ['class' => 'btn btn-success']) . "\n";
echo Html::endTag('div') . "\n";
echo Html::endTag('div') . "\n";

echo Html::endForm() . "\n\n";
