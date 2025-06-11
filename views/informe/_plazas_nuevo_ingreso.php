<?php

use app\models\Centro;
use yii\helpers\Html;
use yii\widgets\DetailView;

foreach ($nuevos_ingresos as $nuevo_ingreso) {
    $centro = Centro::findOne($nuevo_ingreso->COD_CENTRO);
    $year = $nuevo_ingreso->ANO_ACADEMICO; ?>
    <div style='text-align: center; color: #777;'>
        <p style="font-size: 140%;"><?php echo Yii::t('cati', 'Oferta/Matrícula'); ?></p>
        <p style="font-size: 120%;">
            <?php echo Yii::t('cati', 'Año académico'); ?>: <?php echo $year; ?>/<?php echo $year + 1; ?>
        </p>

        <p>
            <b><?php echo Yii::t('cati', 'Estudio'); ?>:</b> <?php echo Html::encode($estudio->nombre); ?><br>
            <b><?php echo Yii::t('cati', 'Centro'); ?>:</b> <?php echo Html::encode($centro->nombre); ?><br>
            <b><?php echo Yii::t('cati', 'Datos a fecha'); ?>:</b>
            <?php echo date('d-m-Y', strtotime($nuevo_ingreso->A_FECHA)); ?>
        </p>
    </div>
    <?php
    echo "<div class='table-responsive'>";
    echo DetailView::widget([
        'model' => $nuevo_ingreso,
        'attributes' => [
            'PLAZAS_OFERTADAS',
            'NUMERO_SOLICITUDES_1',
            'NUMERO_SOLICITUDES',
            'NUM_NUEVO_INGRESO',
        ],
        'options' => ['class' => 'table table-striped table-hover detail-view'],
        'template' => '<tr><th {captionOptions}>{label}</th><td style="text-align: right;" {contentOptions}>{value}</td></tr>',
    ]);
    echo '</div>';
} // endforeach
?>
