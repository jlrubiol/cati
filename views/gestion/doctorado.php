<?php
/**
 * Vista de la página de gestión de la Unidad de Calidad y Racionalización.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use app\models\Calendario;
use app\models\Estudio;
use yii\helpers\Html;

$this->title = Yii::t('gestion', 'Gestión de la web de estudios');
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);

$anyo_academico = Calendario::getAnyoAcademico();
$anterior_anyo_academico = $anyo_academico - 1;
$anyo_encuestas = (date('m') < 11) ? $anyo_academico - 2 : $anyo_academico - 1;  // FIXME
if ($anyo_academico < date('Y') or date('m') > 6) {
    $anyo_profesorado = $anyo_academico - 1 ;
} else {
    $anyo_profesorado = $anyo_academico - 2;
}

$anyo_doctorado = Calendario::getAnyoDoctorado();
$anterior_anyo_doctorado = $anyo_doctorado - 1;
$anteante_anyo_doctorado = $anterior_anyo_doctorado - 1;
$siguiente_anyo_doctorado = $anyo_doctorado + 1;
$siguesigue_anyo_doctorado = $siguiente_anyo_doctorado + 1;
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>


<h2><?php echo Yii::t('cati', 'Doctorado'); ?></h2>


<!-- Agentes Doctorado -->

<h3><?php echo Yii::t('cati', 'Agentes'); ?></h3>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Delegados de los coordinadores de Doctorado'),
        ['//agente/lista-delegados-doct']
    ); ?></li>
</ul>


<!-- Encuestas Doctorado -->

<h3><?php echo Yii::t('cati', 'Encuestas'); ?></h3>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Actualizar informes de encuestas'),
        ['gestion/actualizar-encuestas']
    ); ?></li>
</ul>


<!-- Informes de evaluación Doctorado -->

<h3><?php echo Yii::t('cati', 'Informes de evaluación'); ?></h3>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Informes de doctorado') . ' ' . ($anterior_anyo_doctorado - 1). '/' . $anterior_anyo_doctorado,
        ['gestion/lista-informes', 'anyo' => ($anterior_anyo_doctorado - 1), 'tipo' => 'doctorado']
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Informes de doctorado') . ' ' . $anterior_anyo_doctorado . '/' . $anyo_doctorado,
        ['gestion/lista-informes', 'anyo' => $anterior_anyo_doctorado, 'tipo' => 'doctorado']
    ); ?></li>

    <!--
    <li><?php echo Html::a(
        Yii::t('gestion', 'Informes de doctorado') . ' ' . $anyo_doctorado . '/' . ($anyo_doctorado + 1),
        ['gestion/lista-informes', 'anyo' => $anyo_doctorado, 'tipo' => 'doctorado']
    ); ?></li>
    -->

    <li><?php echo Html::a(
        Yii::t('gestion', 'Editar los apartados del informe de Doctorado')
          . ' ' . $anterior_anyo_doctorado . '/' . $anyo_doctorado,
        ['informe-pregunta/lista', 'anyo' => $anterior_anyo_doctorado, 'tipo' => 'doctorado']
    ); ?></li>

    <li><a data-toggle="modal" href="#modalPreguntasInformeDoctorado">
            <?php echo Yii::t('gestion', 'Clonar apartados'); ?>
        </a> — <?php echo Yii::t('gestion', 'Duplicar los apartados del informe de Doctorado')
          . ' ' . $anterior_anyo_doctorado . '/' . $anyo_doctorado . ' para el curso '
          . $anyo_doctorado . '/' . ($anyo_doctorado + 1); ?></li>

    <!--
    <li><?php echo Html::a(
        Yii::t('gestion', 'Informe de la Calidad de los Estudios de Doctorado (ICED)')
          . ' ' . $anterior_anyo_doctorado . '/' . $anyo_doctorado,
        ['gestion/lista-informes', 'anyo' => $anterior_anyo_doctorado, 'tipo' => 'iced']
    ); ?></li>
    -->

    <li><?php echo Html::a(
        Yii::t('gestion', 'Ver ICED'),
        ['informe/ver-iced', 'anyo' => $anterior_anyo_doctorado]
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Editar los apartados del ICED')
          . ' ' . $anterior_anyo_doctorado . '/' . ($anyo_doctorado),
        ['informe-pregunta/lista', 'anyo' => $anterior_anyo_doctorado, 'tipo' => 'iced']
    ); ?></li>

    <li><a data-toggle="modal" href="#modalPreguntasICED">
            <?php echo Yii::t('gestion', 'Clonar apartados ICED'); ?>
        </a> — <?php echo Yii::t('gestion', 'Duplicar los apartados del ICED')
          . ' ' . ($anterior_anyo_doctorado - 1) . '/' . $anterior_anyo_doctorado . ' para el curso '
          . $anterior_anyo_doctorado . '/' . $anyo_doctorado; ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Listado de informes y planes de Doctorado'),
        ['site/acpua-doct']
    ); ?> (ACPUA)</li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Cargar a Zaguán'),
        ['cargar-a-zaguan', 'anyo' => $anterior_anyo_doctorado, 'tipo' => 'doctorado']
    ) . sprintf(' %s %d/%d', Yii::t('gestion', 'Curso'), $anterior_anyo_doctorado, $anyo_doctorado); ?></li>
</ul>


<!-- Planes de innovación y mejora Doctorado -->

<h3><?php echo Yii::t('cati', 'Planes de innovación y mejora'); ?></h3>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', "Planes anuales de innovación y mejora de doctorado elaborados en el curso {$anteante_anyo_doctorado}/{$anterior_anyo_doctorado}"),
        ['gestion/lista-planes', 'anyo' => $anteante_anyo_doctorado, 'tipo' => 'doctorado']
    ) . " para el curso {$anterior_anyo_doctorado}/{$anyo_doctorado}"; ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Planes anuales de innovación y mejora de doctorado elaborados en el curso {$anterior_anyo_doctorado}/{$anyo_doctorado}"),
        ['gestion/lista-planes', 'anyo' => $anterior_anyo_doctorado, 'tipo' => 'doctorado']
    ) . " para el curso {$anyo_doctorado}/{$siguiente_anyo_doctorado}"; ?></li>

    <!--
    <li><?php echo Html::a(
        Yii::t('gestion', "Planes anuales de innovación y mejora de doctorado elaborados en el curso {$anyo_doctorado}/{$siguiente_anyo_doctorado}"),
        ['gestion/lista-planes', 'anyo' => $anyo_doctorado, 'tipo' => 'doctorado']
    ) . " para el curso {$siguiente_anyo_doctorado}/{$siguesigue_anyo_doctorado}"; ?></li>
    -->

    <li><?php echo Html::a(
        Yii::t('gestion', "Editar los apartados del Plan Anual de Innovación y Mejora elaborado en el curso {$anterior_anyo_doctorado}/{$anyo_doctorado}"),
        ['plan-pregunta/lista', 'anyo' => $anterior_anyo_doctorado, 'tipo' => 'doctorado']
    ) . " para el curso {$anyo_doctorado}/{$siguiente_anyo_doctorado}"; ?></li>

    <li><a data-toggle="modal" href="#modalPreguntasPlanDoctorado">
            <?php echo Yii::t('gestion', 'Clonar apartados'); ?>
        </a> — <?php echo Yii::t('gestion', "Duplicar los apartados del Plan de innovación y mejora elaborado en el curso {$anterior_anyo_doctorado}/{$anyo_doctorado} al plan a elaborar en el curso {$anyo_doctorado}/{$siguiente_anyo_doctorado}"); ?></li>

    <!--
    <li><?php echo Html::a(
        Yii::t('gestion', "Editar los apartados del Plan Anual de Innovación y Mejora elaborado en el curso {$anyo_doctorado}/{$siguiente_anyo_doctorado}"),
        ['plan-pregunta/lista', 'anyo' => $anyo_doctorado, 'tipo' => 'doctorado']
    ) . " para el curso {$siguiente_anyo_doctorado}/{$siguesigue_anyo_doctorado}"; ?></li>
    -->

    <li><?php echo Html::a(
        Yii::t('gestion', 'Listado de informes y planes de Doctorado'),
        ['site/acpua-doct']
    ); ?> (ACPUA)</li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Cargar a Zaguán los planes elaborados en el curso {$anterior_anyo_doctorado}/{$anyo_doctorado}"),
        ['cargar-a-zaguan', 'anyo' => $anterior_anyo_doctorado, 'tipo' => 'doctorado']
    ) . sprintf(' %s %d/%d', Yii::t('gestion', 'para el curso'), $anterior_anyo_doctorado, $anyo_doctorado); ?></li>
</ul>


<!-- Información general -->

<h3><?php echo Yii::t('cati', 'Información general'); ?></h3>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Editar la información de un programa de doctorado'),
        ['gestion/lista-informaciones', 'tipo' => 'doctorado']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Editar la información de todos los programas'),
        ['informacion/editar-infos-en-masa', 'tipoEstudio_id' => Estudio::DOCT_TIPO_ESTUDIO_ID]
    ); ?></li>
</ul>


<!-- Normativa Doctorado -->

<h3><?php echo Yii::t('cati', 'Normativa'); ?></h3>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('cati', 'Cómo se asegura la calidad del programa'),
        ['pagina/editar', 'id' => 9]
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('cati', 'Procedimientos del sistema interno de gestión de la calidad'),
        ['pagina/editar', 'id' => 10]
    ); ?></li>
</ul>


<!-- Otros -->

<h3><?php echo Yii::t('cati', 'Otros'); ?></h3>

<ul class='listado'>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Actualizar datos académicos de Doctorado'),
        ['gestion/actualizar-datos-doctorado']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Gestión de fechas'),
        ['gestion/calendario/index']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Webs específicas de los planes'),
        ['gestion/ver-webs-especificas', 'tipo' => 'doctorado']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Indicadores de calidad de los programas'),
        ['gestion/lista-indicadores']
    ); ?> (periodo 2013/2014 a 2016-2017)</li>
</ul>
<hr><br>



<!-- Modal Informe Doctorado -->
<div id="modalPreguntasInformeDoctorado" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo Yii::t('gestion', 'Clonar apartados'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?php printf(
                    Yii::t(
                        'gestion',
                        'Se dispone a duplicar los apartados del informe de Doctorado del'
                        . ' curso %d/%d para el curso %d/%d.  Si ya existen apartados para'
                        . ' este curso, serán <b>sobrescritos</b>.'
                    ), $anterior_anyo_doctorado, $anyo_doctorado, $anyo_doctorado, $anyo_doctorado + 1
                ); ?></p>
            </div>
            <div class="modal-footer">
                <?php echo Html::a(
                    Yii::t('gestion', 'Continuar'),
                    [
                        'informe-pregunta/duplicar-preguntas',
                        'anyo_viejo' => $anterior_anyo_doctorado,
                        'anyo_nuevo' => $anyo_doctorado,
                        'tipo' => 'doctorado',
                    ],
                    [
                        'id' => 'continuar-duplicar-preguntas-informe',
                        'class' => 'btn btn-danger',  // Button
                        'title' => Yii::t('gestion', 'Clonar apartados'),
                    ]
                ); ?>
                <button type="button" class="btn btn-info" data-dismiss="modal">
                    <?php echo Yii::t('gestion', 'Cancelar'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal ICED -->
<div id="modalPreguntasICED" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo Yii::t('gestion', 'Clonar apartados'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?php printf(
                    Yii::t(
                        'gestion',
                        'Se dispone a duplicar los apartados del ICED del'
                        . ' curso %d/%d para el curso %d/%d.  Si ya existen apartados para'
                        . ' este curso, serán <b>sobrescritos</b>.'
                    ), ($anterior_anyo_doctorado - 1), $anterior_anyo_doctorado, $anterior_anyo_doctorado, $anyo_doctorado
                ); ?></p>
            </div>
            <div class="modal-footer">
                <?php echo Html::a(
                    Yii::t('gestion', 'Continuar'),
                    [
                        'informe-pregunta/duplicar-preguntas',
                        'anyo_viejo' => $anterior_anyo_doctorado - 1,
                        'anyo_nuevo' => $anterior_anyo_doctorado,
                        'tipo' => 'iced',
                    ],
                    [
                        'id' => 'continuar-duplicar-preguntas-iced',
                        'class' => 'btn btn-danger',  // Button
                        'title' => Yii::t('gestion', 'Clonar apartados'),
                    ]
                ); ?>
                <button type="button" class="btn btn-info" data-dismiss="modal">
                    <?php echo Yii::t('gestion', 'Cancelar'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal PAIM Doctorado -->
<div id="modalPreguntasPlanDoctorado" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo Yii::t('gestion', 'Clonar apartados'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?php printf(
                    Yii::t(
                        'gestion',
                        'Se dispone a duplicar los apartados del Plan de innovación y'
                        . ' mejora del curso %d/%d para el curso %d/%d.  Si ya existen'
                        . ' apartados para este curso, serán <b>sobrescritos</b>.'
                    ), $anterior_anyo_doctorado, $anyo_doctorado, $anyo_doctorado, $anyo_doctorado + 1
                ); ?></p>
            </div>
            <div class="modal-footer">
                <?php echo Html::a(
                    Yii::t('gestion', 'Continuar'),
                    [
                        'plan-pregunta/duplicar-preguntas',
                        'anyo_viejo' => $anterior_anyo_doctorado,
                        'anyo_nuevo' => $anyo_doctorado,
                        'tipo' => 'doctorado',
                    ],
                    [
                        'id' => 'continuar',
                        'class' => 'btn btn-danger',    // Button
                        'title' => Yii::t('gestion', 'Clonar apartados')
                    ]
                ); ?>
                <button type="button" class="btn btn-info" data-dismiss="modal">
                    <?php echo Yii::t('gestion', 'Cancelar'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
