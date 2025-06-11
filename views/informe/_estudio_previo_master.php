<?php

use app\models\Centro;
use app\models\Estudio;
use yii\grid\GridView;
use yii\helpers\Html;

$subnum_tabla = 0;
foreach ($dpsEstudiosPrevios as $centro_id => $dpEstudiosPrevios) {
    $subnum_tabla++;
    $titulo = (isset($apartado) ? "Tabla {$apartado}.{$num_tabla}.{$subnum_tabla}: " : '' ) . Yii::t('cati', 'Estudio previo de los estudiantes de nuevo ingreso');
    $anyo_acad = Yii::t('cati', 'Año académico');
    $siguiente_anyo = $anyo + 1;
    $est = Yii::t('cati', 'Estudio');
    $nombre_est = Html::encode($estudio->nombre);
    $cent = Yii::t('cati', 'Centro');
    // $nombre_cent = Html::encode($estudio->plans[0]->centro->nombre);
    $nombre_centro = Centro::getCentro($centro_id)->nombre;
    $fecha = Yii::t('cati', 'Datos a fecha');
    $dato_fecha = isset($dpEstudiosPrevios->getModels()[0])
        ? date('d-m-Y', strtotime($dpEstudiosPrevios->getModels()[0]['A_FECHA']))
        : '';
    $caption = <<< CAPTION
    <div style='text-align: center; color: #777;'>
    <p style='font-size: 140%;'>$titulo</p>
    <p style='font-size: 120%;'>$anyo_acad: $anyo/$siguiente_anyo</p>
    <p><b>$est:</b> $nombre_est<br><b>$cent:</b> $nombre_centro<br><b>$fecha:</b> $dato_fecha</p>
    </div>
    CAPTION;

    echo "<div class='table-responsive'>";
    echo GridView::widget([
        'caption' => $caption,
        'columns' => [
            'NOMBRE_ESTUD_MEC_PREVIO_MASTER',
            [
                'attribute' => 'NUM_ALUMNOS_POR_ESTUDIO_PREVIO',
                'contentOptions' => ['style' => 'text-align: right;'],
                'headerOptions' => ['style' => 'text-align: right;'],
            ],
        ],
        'dataProvider' => $dpEstudiosPrevios,
        'options' => ['class' => 'cabecera-azul'],
        'summary' => false,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
    ]);
    echo '</div>';
}
