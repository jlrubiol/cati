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
$anteante_anyo_academico = $anterior_anyo_academico - 1;
$siguiente_anyo_academico = $anyo_academico + 1;
$siguesigue_anyo_academico = $siguiente_anyo_academico + 1;

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


<!-- —————————————————————————————— Grado y Máster —————————————————————————————— -->

<!-- Acreditación -->
<h2><?php echo Yii::t('cati', 'Acreditación'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Acreditación institucional de los centros'),
        ['gestion/ver-centros-acreditacion',]
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Acreditación de los estudios'),
        ['gestion/ver-acreditacion-estudios',]
    ); ?></li>

    <li><?php echo Html::a(
       Yii::t('gestion', 'Periodos de evaluación'),
        ['gestion/ver-periodos-evaluacion', 'anyo'  => $anterior_anyo_academico]
    ); ?></li>
</ul>

<!-- Agentes Grado y Máster -->

<h2><?php echo Yii::t('cati', 'Agentes'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Agentes de las titulaciones de Grado y Máster'),
        ['gestion/ver-agentes']
    ); ?><br><br></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Coordinadores de los planes de Grado y Máster'),
        ['gestion/ver-coordinadores']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Direcciones de correo de los coordinadores de Grado y Máster'),
        ['gestion/ver-correos-coordinadores']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Delegados de los coordinadores de cada plan de Grado y Máster'),
        ['//agente/lista-delegados']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Lista de todos los delegados de coordinadores '),
        ['//agente/ver-delegados']
    ); ?><br><br></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Presidentes de las comisiones de garantía'),
        ['gestion/ver-presidentes']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Direcciones de correo de los presidentes de las comisiones de garantía'),
        ['gestion/ver-correos-presidentes']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Delegados de los presidentes de la CGC de cada plan de Grado y Máster'),
        ['//agente/lista-delegados-cgc']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Lista de todos los delegados de CGC '),
        ['//agente/ver-delegados-cgc']
    ); ?><br><br></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Direcciones de correo de los expertos del rector en CEC'),
        ['gestion/ver-correos-expertos']
    ); ?></li>
</ul>


<!-- Encuestas Grado y Máster -->

<h2><?php echo Yii::t('cati', 'Encuestas'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Histórico de informes de encuestas de Grado y Máster'),
        ['site/ver-encuestas', 'anyo' => $anyo_encuestas]
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Actualizar informes de encuestas'),
        ['gestion/actualizar-encuestas']
    ); ?></li>
</ul>


<!-- Fechas Grado y Máster -->

<h2><?php echo Yii::t('cati', 'Fechas'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Gestión de fechas'),
        ['gestion/calendario/index']
    ); ?></li>
</ul>


<!-- Informes de Evaluación de la Calidad - Grado y Máster -->

<h2><?php echo Yii::t('cati', 'Informes de Evaluación de la Calidad'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Informes de Grado y Máster') . ' ' . ($anterior_anyo_academico - 1) . '/' . $anterior_anyo_academico,
        ['gestion/lista-informes', 'anyo' => ($anterior_anyo_academico - 1), 'tipo' => 'grado-master']
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Informes de Grado y Máster') . ' ' . $anterior_anyo_academico . '/' . $anyo_academico,
        ['gestion/lista-informes', 'anyo' => $anterior_anyo_academico, 'tipo' => 'grado-master']
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Extractos de los informes de Grado y Máster') . ' '
          . ($anterior_anyo_academico - 1) . '/' . $anterior_anyo_academico,
        ['gestion/seleccionar-pregunta', 'anyo' => ($anterior_anyo_academico - 1), 'tipo' => 'grado-master']
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Extractos de los informes de Grado y Máster') . ' ' . $anterior_anyo_academico . '/' . $anyo_academico,
        ['gestion/seleccionar-pregunta', 'anyo' => $anterior_anyo_academico, 'tipo' => 'grado-master']
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Editar los apartados del informe de Grado y Máster')
          . ' ' . $anterior_anyo_academico . '/' . $anyo_academico,
        ['informe-pregunta/lista', 'anyo' => $anterior_anyo_academico, 'tipo' => 'grado-master']
    ); ?></li>

    <li><a data-toggle="modal" href="#modalPreguntasInforme">
            <?php echo Yii::t('gestion', 'Clonar apartados'); ?>
        </a> — <?php echo Yii::t('gestion', 'Duplicar los apartados del informe de Grado y Máster')
          . ' ' . ($anterior_anyo_academico) . '/' . $anyo_academico . ' para el curso '
          . $anyo_academico . '/' . ($anyo_academico + 1); ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Listado de informes y planes de Grado y Máster'),
        ['site/acpua']
    ); ?> (ACPUA)</li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Cargar a Zaguán'),
        ['cargar-a-zaguan', 'anyo' => $anterior_anyo_academico, 'tipo' => 'grado-master']
    ) . sprintf(' %s %d/%d', Yii::t('gestion', 'Curso'), $anterior_anyo_academico, $anyo_academico); ?></li>
</ul>


<!-- Planes de innovación y mejora Grado y Máster -->

<h2><?php echo Yii::t('cati', 'Planes de innovación y mejora'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', "Planes de innovación y mejora para el curso {$anterior_anyo_academico}/{$anyo_academico}."),
        ['gestion/lista-planes', 'anyo' => $anteante_anyo_academico, 'tipo' => 'grado-master']
    ) . " Campaña {$anterior_anyo_academico}"; ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Planes de innovación y mejora para el curso {$anyo_academico}/{$siguiente_anyo_academico}."),
        ['gestion/lista-planes', 'anyo' => $anterior_anyo_academico, 'tipo' => 'grado-master']
    ) . " Campaña {$anyo_academico}"; ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Listado de acciones PAIM de todos los estudios para el curso {$anterior_anyo_academico}/{$anyo_academico}."),
        ['gestion/seleccionar-pregunta-plan', 'anyo' => $anteante_anyo_academico, 'tipo' => 'grado-master']
    ). " Campaña {$anterior_anyo_academico}"; ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Listado de acciones PAIM de todos los estudios para el curso {$anyo_academico}/{$siguiente_anyo_academico}."),
        ['gestion/seleccionar-pregunta-plan', 'anyo' => $anterior_anyo_academico, 'tipo' => 'grado-master']
    ) . " Campaña {$anyo_academico}"; ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Listado de acciones PAIM de un centro para el curso {$anterior_anyo_academico}/{$anyo_academico}."),
        ['gestion/seleccionar-centro-paim', 'anyo' => $anteante_anyo_academico]
    ). " Campaña {$anterior_anyo_academico}"; ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Listado de acciones PAIM de un centro  para el curso {$anyo_academico}/{$siguiente_anyo_academico}."),
        ['gestion/seleccionar-centro-paim', 'anyo' => $anterior_anyo_academico]
    ) . " Campaña {$anyo_academico}"; ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Editar los apartados del Plan de innovación y mejora para el curso {$anyo_academico}/{$siguiente_anyo_academico}."),
        ['plan-pregunta/lista', 'anyo' => $anterior_anyo_academico, 'tipo' => 'grado-master']
    ) . " Campaña {$anyo_academico}"; ?></li>

    <li><a data-toggle="modal" href="#modalPreguntasPlan">
            <?php echo Yii::t('gestion', 'Clonar apartados y opciones'); ?>
        </a> — <?php echo Yii::t('gestion', "Duplicar los apartados y opciones de los desplegables del PAIM de la campaña {$anyo_academico} al PAIM de la campaña {$siguiente_anyo_academico}"); ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Editar las opciones de los desplegables de los PAIM'),
        ['paim-opcion/index']
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t(
            'gestion',
            'Listado de informes y planes de Grado y Máster'
        ),  // . ' ' . $anyo_academico . '/' . ($anyo_academico + 1),
        ['site/acpua']
    ); ?> (ACPUA)</li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Cargar a Zaguán los planes para el curso {$anyo_academico}/{$siguiente_anyo_academico}."),
        ['cargar-a-zaguan', 'anyo' => $anterior_anyo_academico, 'tipo' => 'grado-master']
    ) . " Campaña {$anyo_academico}"; ?></li>
</ul>

<!-- Modal Informe Grado y Máster -->
<div id="modalPreguntasInforme" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo Yii::t('gestion', 'Clonar apartados'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?php
                printf(
                    Yii::t(
                        'gestion',
                        'Se dispone a duplicar los apartados del informe de Grado y Máster'
                        . ' del curso %d/%d para el curso %d/%d.  Si ya existen apartados'
                        . ' para este curso, serán <b>sobrescritos</b>.'
                    ),
                    $anterior_anyo_academico,
                    $anyo_academico,
                    $anyo_academico,
                    ($anyo_academico + 1)
                );
                ?></p>
            </div>
            <div class="modal-footer">
                <?php
                echo Html::a(
                    Yii::t('gestion', 'Continuar'),
                    [
                        'informe-pregunta/duplicar-preguntas',
                        'anyo_viejo' => $anterior_anyo_academico,
                        'anyo_nuevo' => $anyo_academico,
                        'tipo' => 'grado-master',
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


<!-- Modal PAIM Grado y Máster -->
<div id="modalPreguntasPlan" class="modal fade" role="dialog">
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
                        "Se dispone a duplicar los apartados y las opciones de los desplegables del PAIM para el curso {$anyo_academico}/{$siguiente_anyo_academico} (campaña {$anyo_academico})"
                        .  " al PAIM para el curso {$siguiente_anyo_academico}/{$siguesigue_anyo_academico} (campaña {$siguiente_anyo_academico}).<br>"
                        . 'Si ya existen apartados para ese curso, serán <b>sobrescritos</b>.'
                    )
                ); ?></p>
            </div>
            <div class="modal-footer">
                <?php echo Html::a(
                    Yii::t('gestion', 'Continuar'),
                    [
                        'plan-pregunta/duplicar-preguntas',
                        'anyo_viejo' => $anterior_anyo_academico,
                        'anyo_nuevo' => $anyo_academico,
                        'tipo' => 'grado-master',
                    ],
                    ['id' => 'continuar', 'class' => 'btn btn-danger', 'title' => Yii::t('gestion', 'Clonar apartados')]  // Button
                ); ?>
                <button type="button" class="btn btn-info" data-dismiss="modal">
                    <?php echo Yii::t('gestion', 'Cancelar'); ?>
                </button>
            </div>
        </div>
    </div>
</div>



<!-- Normativa Grado y Máster -->

<h2><?php echo Yii::t('cati', 'Normativa'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('cati', 'Cómo se asegura la calidad de la titulación'),
        ['pagina/editar', 'id' => 1]
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('cati', 'Normativa de calidad de las titulaciones'),
        ['pagina/editar', 'id' => 2]
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('cati', 'Comisión de garantía de la calidad de la titulación'),
        ['pagina/editar', 'id' => 3]
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('cati', 'Coordinador de titulación'),
        ['pagina/editar', 'id' => 4]
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('cati', 'Comisión de evaluación de la calidad'),
        ['pagina/editar', 'id' => 5]
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('cati', 'Plan anual de innovación y calidad'),
        ['pagina/editar', 'id' => 6]
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('cati', 'Procedimientos básicos de funcionamiento del sistema interno de gestión de calidad de las titulaciones'),
        ['pagina/editar', 'id' => 7]
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('cati', 'Informes y resultados'),
        ['pagina/editar', 'id' => 8]
    ); ?></li>
</ul>

<ul class='listado'>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Subir procedimiento del SIGC'),
        'subir-procedimiento'
    ); ?></li>
</ul>


<!-- Profesorado Grado y Máster -->

<h2><?php echo Yii::t('cati', 'Profesorado'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        sprintf('%s %d/%d', Yii::t('gestion', 'Estructura del profesorado'), $anyo_profesorado, $anyo_profesorado + 1),
        ['gestion/ver-estructura', 'anyo' => $anyo_profesorado]
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Evolución del profesorado'),
        ['gestion/lista-evolucion-profesorado']
    ); ?></li>
</ul>


<!-- Resultados Grado y Máster -->

<h2><?php echo Yii::t('cati', 'Resultados'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Resultados académicos de Grado y Máster') . ' ' . $anyo_academico . '/' . ($anyo_academico + 1),
        ['gestion/ver-resultados-academicos', 'anyo' => $anyo_academico]
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Actualizar datos académicos de Grado y Máster'),
        ['gestion/actualizar-datos-academicos']
    ); ?></li>
</ul>


<!-- Otros Grado y Máster -->

<h2><?php echo Yii::t('cati', 'Otros'); ?></h2>

<ul class='listado'>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Webs específicas de los planes'),
        ['gestion/ver-webs-especificas', 'tipo' => 'grado-master']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Suplantar a un usuario'),
        ['suplantacion/index']
    ); ?></li>
</ul>

<hr>

<!-- ————————————————————————————————— Doctorado ————————————————————————————————— -->

<h2><?php echo Yii::t('cati', 'Doctorado'); ?></h2>


<!-- Agentes Doctorado -->

<h2><?php echo Yii::t('cati', 'Agentes'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Delegados de los coordinadores de cada Programa de Doctorado'),
        ['//agente/lista-delegados-doct']
    ); ?></li>
</ul>


<!-- Encuestas Doctorado -->

<h3><?php echo Yii::t('cati', 'Encuestas'); ?></h3>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Actualizar encuestas'),
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

    <li><?php echo Html::a(
        Yii::t('gestion', 'Informe de la Calidad de los Estudios de Doctorado (ICED)')
          . ' ' . $anterior_anyo_doctorado . '/' . $anyo_doctorado,
        ['gestion/lista-informes', 'anyo' => $anterior_anyo_doctorado, 'tipo' => 'iced']
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Editar los apartados del ICED')
          . ' ' . $anterior_anyo_doctorado . '/' . ($anyo_doctorado),
        ['informe-pregunta/lista', 'anyo' => $anterior_anyo_doctorado, 'tipo' => 'iced']
    ); ?></li>

    <li><a data-toggle="modal" href="#modalPreguntasICED">
            <?php echo Yii::t('gestion', 'Clonar apartados ICED'); ?>
        </a> — <?php echo Yii::t('gestion', 'Duplicar los apartados del ICED')
          . ' ' . ($anterior_anyo_doctorado) . '/' . $anyo_doctorado . ' para el curso '
          . $anyo_doctorado . '/' . ($anyo_doctorado + 1); ?></li>

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
        Yii::t('gestion', "Planes anuales de innovación y mejora de doctorado para el curso {$anterior_anyo_doctorado}/{$anyo_doctorado}."),
        ['gestion/lista-planes', 'anyo' => $anteante_anyo_doctorado, 'tipo' => 'doctorado']
    ) . " Campaña {$anterior_anyo_doctorado}"; ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Planes anuales de innovación y mejora de doctorado para el curso {$anyo_doctorado}/{$siguiente_anyo_doctorado}."),
        ['gestion/lista-planes', 'anyo' => $anterior_anyo_doctorado, 'tipo' => 'doctorado']
    ) . " Campaña {$anyo_doctorado}"; ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Listado de acciones PAIM para el curso {$anterior_anyo_doctorado}/{$anyo_doctorado}."),
        ['gestion/extractos-paim-centro', 'anyo' => $anteante_anyo_doctorado, 'centro_id' => 160]
    ) . " Campaña {$anterior_anyo_doctorado}"; ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Listado de acciones PAIM para el curso {$anyo_doctorado}/{$siguiente_anyo_doctorado}."),
        ['gestion/extractos-paim-centro', 'anyo' => $anterior_anyo_doctorado, 'centro_id' => 160]
    ) . " Campaña {$anyo_doctorado}"; ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Editar los apartados del Plan Anual de Innovación y Mejora para el curso {$anyo_doctorado}/{$siguiente_anyo_doctorado}."),
        ['plan-pregunta/lista', 'anyo' => $anterior_anyo_doctorado, 'tipo' => 'doctorado']
    ) . " Campaña {$anyo_doctorado}"; ?></li>

    <li><a data-toggle="modal" href="#modalPreguntasPlanDoctorado">
            <?php echo Yii::t('gestion', 'Clonar apartados y opciones'); ?>
        </a> — <?php echo Yii::t('gestion', "Duplicar los apartados y opciones de los desplegables del PAIM de la campaña {$anyo_doctorado} al PAIM de la campaña {$siguiente_anyo_doctorado}"); ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Editar las opciones de los desplegables de los PAIM'),
        ['paim-opcion/index']
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('gestion', 'Listado de informes y planes de Doctorado'),
        ['site/acpua-doct']
    ); ?> (ACPUA)</li>

    <li><?php echo Html::a(
        Yii::t('gestion', "Cargar a Zaguán los planes para el curso {$anyo_doctorado}/{$siguiente_anyo_doctorado}."),
        ['cargar-a-zaguan', 'anyo' => $anterior_anyo_doctorado, 'tipo' => 'doctorado']
    ) . " Campaña {$anyo_doctorado}"; ?></li>
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
                    ),
        $anterior_anyo_doctorado,
        $anyo_doctorado,
        $anyo_doctorado,
        ($anyo_doctorado + 1)
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
                    ),
                    $anterior_anyo_doctorado,
                    $anyo_doctorado,
                    $anyo_doctorado,
                    ($anyo_doctorado + 1)
                ); ?></p>
            </div>
            <div class="modal-footer">
                <?php echo Html::a(
                    Yii::t('gestion', 'Continuar'),
                    [
                        'informe-pregunta/duplicar-preguntas',
                        'anyo_viejo' => $anterior_anyo_doctorado,
                        'anyo_nuevo' => $anyo_doctorado,
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
                        "Se dispone a duplicar los apartados y las opciones de los desplegables del PAIM para el curso {$anyo_doctorado}/{$siguiente_anyo_doctorado} (campaña {$anyo_doctorado})"
                        . " al PAIM para el curso {$siguiente_anyo_doctorado}/{$siguesigue_anyo_doctorado} (campaña {$siguiente_anyo_doctorado}).<br>"
                        . 'Si ya existen apartados para ese curso, serán <b>sobrescritos</b>.'
                    )
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
