<?php
use yii\helpers\Html;
use yii\helpers\Url;

// Al añadir/quitar/renombrar las solapas, modificar también
// * informacion/editar-infos.php
// * informacion/editar-infos-en-masa.php
// * el array $ids en _tabpanes_doct.
?>

<ul class="nav nav-tabs tabs-left"><!-- 'tabs-right' for right tabs -->
    <li class='active'>
        <a href="#info" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('cati', 'Información general'); ?></a></li>
    <li><a href="#competencias" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('doct', 'Competencias'); ?></a></li>
    <li><a href="#admision" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('doct', 'Acceso, admisión y matrícula'); ?></a></li>
    <li><a href="#organizacion" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('doct', 'Supervisión y seguimiento'); ?></a></li>
    <li><a href="#actividades" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('doct', 'Actividades formativas y movilidad'); ?></a></li>
    <li><a href="#rrhh" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('doct', 'Profesorado. Líneas y equipos de investigación'); ?></a></li>
    <li><a href="#rrmm" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('doct', 'Recursos y planificación'); ?></a></li>

    <li><?php echo Html::a(
        '<span class="topogt">&gt; &nbsp;</span>'.Yii::t('cati', 'Calidad'),
        Url::to('#calidad'),
        ['data-toggle' => 'tab']
); ?></li>

    <li><?php echo Html::a(
        '<span class="topogt">&gt; &nbsp;</span>'.Yii::t('doct', 'Indicadores y resultados de encuestas'),
        ['', '#' => 'encuestas', 'id' => $estudio->id],
        ['data-toggle' => 'tab']
    ); ?></li>

    <li><?php echo Html::a(
        '<span class="topogt">&gt;&nbsp;</span>' . Yii::t('doct', 'Información gráfica del estudio')
            . ' <span class="glyphicon glyphicon-link"></span>',
        "https://segeda.unizar.es/doctorado.html?estudio={$estudio->id_nk}",
        ['target' => '_blank']
    ); ?></li>
</ul>
