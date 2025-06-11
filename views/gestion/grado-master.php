<?php
/**
 * Vista de la página de gestión de la Unidad de Calidad y Racionalización.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
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
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<!-- Agentes Grado y Máster -->

<h2><?php echo Yii::t('cati', 'Agentes'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Agentes de las titulaciones de Grado y Máster'),
        ['gestion/ver-agentes']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Coordinadores de los planes de Grado y Máster'),
        ['gestion/ver-coordinadores']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Direcciones de correo de los coordinadores de Grado y Máster'),
        ['gestion/ver-correos-coordinadores']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Delegados de los coordinadores de Grado y Máster'),
        ['//agente/lista-delegados']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Presidentes de las comisiones de garantía'),
        ['gestion/ver-presidentes']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Direcciones de correo de los presidentes de las comisiones de garantía'),
        ['gestion/ver-correos-presidentes']
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


<!-- Horarios Grado y Máster -->

<h2><?php echo Yii::t('cati', 'Horarios'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Horarios de Grado y Máster'),
        ['gestion/ver-horarios']
    ); ?></li>
</ul>


<!-- Información general Grado y Máster -->

<h2><?php echo Yii::t('cati', 'Información general'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Editar la información de un estudio de Grado o Máster'),
        ['gestion/lista-informaciones', 'tipo' => 'grado-master']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Editar la información de todos los grados'),
        ['informacion/editar-infos-en-masa', 'tipoEstudio_id' => Estudio::GRADO_TIPO_ESTUDIO_ID]
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('gestion', 'Editar la información de todos los másteres'),
        ['informacion/editar-infos-en-masa', 'tipoEstudio_id' => Estudio::MASTER_TIPO_ESTUDIO_ID]
    ); ?></li>
</ul>


<!-- Notas de los planes de estudios Grado y Máster -->

<h2><?php echo Yii::t('cati', 'Notas de los planes de estudios'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('gestion', 'Editar las notas de los planes de estudios'),
        ['gestion/lista-notas-planes']
    ); ?></li>
</ul>
