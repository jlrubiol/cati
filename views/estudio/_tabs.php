<?php
use yii\helpers\Html;

// Al añadir/quitar/renombrar las solapas, modificar también
// informacion/editar-infos.php
// informacion/editar-infos-en-masa
// y el array $ids en _tabpanes.php .
?>

<ul class="nav nav-tabs tabs-left"><!-- 'tabs-right' for right tabs -->
    <li class="active">
        <a href="#inicio" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('cati', 'Inicio'); ?></a></li>
    <li><a href="#acceso" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('cati', 'Acceso y admisión'); ?></a></li>
    <li><a href="#perfiles" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('cati', 'Perfiles de salida'); ?></a></li>
    <li><a href="#queaprende" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('cati', 'Qué se aprende'); ?></a></li>
    <li><a href="#planes" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('cati', 'Plan de estudios'); ?></a></li>
    <li><a href="#apoyo" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('cati', 'Apoyo al estudiante'); ?></a></li>
    <li><a href="#profesorado" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('cati', 'Profesorado'); ?></a></li>
    <li><a href="#calidad" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('cati', 'Calidad'); ?></a></li>
    <li><a href="#encuestas" data-toggle="tab"><span class="topogt">&gt;&nbsp;</span>
        <?php echo Yii::t('cati', 'Encuestas y resultados'); ?></a></li>

    <li><?php echo Html::a(
        '<span class="topogt">&gt;&nbsp;</span>' . Yii::t('cati', 'Información gráfica del estudio')
            . ' <span class="glyphicon glyphicon-link"></span>',
       "https://segeda.unizar.es/titulaciones_estcen.html?estudio={$estudio->id_nk}",
        ['target' => '_blank']
    ); ?></li>
</ul>
