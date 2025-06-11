<?php

use app\models\Centro;
use yii\grid\GridView;
use yii\helpers\Html;

$anyo_str = Yii::t('cati', 'Año académico');
$anyo2 = $anyo + 1;
$estudio_str = Yii::t('cati', 'Estudio');
$nombre_estudio = Html::encode($estudio->nombre);

$subnum_tabla = 0;
foreach ($dpsCalificaciones as $centro_id => $dpCalificaciones) :
    if ($dpCalificaciones->totalCount == 0) {
        continue;
    }

    $subnum_tabla++;
    $titulo = (isset($apartado) ? "Tabla {$apartado}.{$num_tabla}.{$subnum_tabla}: " : '' ) . Yii::t('cati', 'Distribución de calificaciones');

    $centro_str = Yii::t('cati', 'Centro');
    $centro = Centro::findOne($centro_id);
    $nombre_centro = Html::encode($centro->nombre);
    $fecha_str = Yii::t('cati', 'Datos a fecha');
    $dato_fecha = isset($dpCalificaciones->getModels()[0])
        ? date('d-m-Y', strtotime($dpCalificaciones->getModels()[0]['A_FECHA']))
        : '—';
    $caption = <<< CAPTION
    <div style='text-align: center; color: #777;'>
        <p style='font-size: 140%;'>$titulo</p>
        <p style='font-size: 120%;'>$anyo_str: $anyo/$anyo2</p>
        <p>
            <b>$estudio_str:</b> $nombre_estudio<br>
            <b>$centro_str:</b> $nombre_centro<br>
            <b>$fecha_str:</b> $dato_fecha
        </p>
    </div>
CAPTION;

    echo "<div class='table-responsive'>";
    echo GridView::widget(
        [
            'caption' => $caption,
            'columns' => [
                [
                    'attribute' => 'PRELA_CU',
                    'contentOptions' => ['style' => 'padding: 8px 2px 8px;'],
                    'headerOptions' => ['style' => 'padding: 0.375em 2px;'],
                    'label' => Yii::t('cati', 'Curso'),
                ], [
                    'attribute' => 'COD_ASIGNATURA',
                    'contentOptions' => ['style' => 'padding: 8px 2px 8px;'],
                    'headerOptions' => ['style' => 'padding: 0.375em 2px;'],
                    'label' => Yii::t('cati', 'Código'),
                ], [
                    'attribute' => 'DENOM_ASIGNATURA',
                    'contentOptions' => ['style' => 'padding: 8px 2px 8px; max-width: 300px;'],
                    'headerOptions' => ['style' => 'padding: 0.375em 2px;'],
                    'label' => Yii::t('cati', 'Asignatura'),
                ], [
                    'attribute' => 'ALUMNOS_NO_PRESENTADOS',
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => [  // HTML attributes for the header cell tag
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'style' => 'text-align: right; padding: 0.375em 2px;',
                        'title' => Yii::t('cati', 'No presentados'),
                    ],
                    'label' => Yii::t('cati', 'No pre'),
                ], [
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => ['style' => 'text-align: right; padding: 0.375em 2px;'],
                    'label' => '%',
                    'value' => function ($model) {
                        return $model['TOTAL_ALUMNOS'] ? $model['ALUMNOS_NO_PRESENTADOS'] * 100 / $model['TOTAL_ALUMNOS'] : 0;
                    },
                    'format' => ['decimal', 1],
                ], [
                    'attribute' => 'ALUMNOS_SUSPENDIDOS',
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => [  // HTML attributes for the header cell tag
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'style' => 'text-align: right; padding: 0.375em 2px;',
                        'title' => Yii::t('cati', 'Suspendidos'),
                    ],
                    'label' => Yii::t('cati', 'Sus'),
                ], [
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => ['style' => 'text-align: right; padding: 0.375em 2px;'],
                    'label' => '%',
                    'value' => function ($model) {
                        return $model['TOTAL_ALUMNOS'] ? $model['ALUMNOS_SUSPENDIDOS'] * 100 / $model['TOTAL_ALUMNOS'] : 0;
                    },
                    'format' => ['decimal', 1],
                ], [
                    'attribute' => 'NUMERO_APROBADO',
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => [  // HTML attributes for the header cell tag
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'style' => 'text-align: right; padding: 0.375em 2px;',
                        'title' => Yii::t('cati', 'Aprobados'),
                    ],
                    'label' => Yii::t('cati', 'Apr'),
                ], [
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => ['style' => 'text-align: right; padding: 0.375em 2px;'],
                    'label' => '%',
                    'value' => function ($model) {
                        return $model['TOTAL_ALUMNOS'] ? $model['NUMERO_APROBADO'] * 100 / $model['TOTAL_ALUMNOS'] : 0;
                    },
                    'format' => ['decimal', 1],
                ], [
                    'attribute' => 'NUMERO_NOTABLE',
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => [  // HTML attributes for the header cell tag
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'style' => 'text-align: right; padding: 0.375em 2px;',
                        'title' => Yii::t('cati', 'Notable'),
                    ],
                    'label' => Yii::t('cati', 'Not'),
                ], [
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => ['style' => 'text-align: right; padding: 0.375em 2px;'],
                    'label' => '%',
                    'value' => function ($model) {
                        return $model['TOTAL_ALUMNOS'] ? $model['NUMERO_NOTABLE'] * 100 / $model['TOTAL_ALUMNOS'] : 0;
                    },
                    'format' => ['decimal', 1],
                ], [
                    'attribute' => 'NUMERO_SOBRESALIENTE',
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => [  // HTML attributes for the header cell tag
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'style' => 'text-align: right; padding: 0.375em 2px;',
                        'title' => Yii::t('cati', 'Sobresaliente'),
                    ],
                    'label' => Yii::t('cati', 'Sob'),
                ], [
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => ['style' => 'text-align: right; padding: 0.375em 2px;'],
                    'label' => '%',
                    'value' => function ($model) {
                        return $model['TOTAL_ALUMNOS'] ? $model['NUMERO_SOBRESALIENTE'] * 100 / $model['TOTAL_ALUMNOS'] : 0;
                    },
                    'format' => ['decimal', 1],
                ], [
                    'attribute' => 'NUMERO_MATRICULA_HONOR',
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => [  // HTML attributes for the header cell tag
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'style' => 'text-align: right; padding: 0.375em 2px;',
                        'title' => Yii::t('cati', 'Matrícula de honor'),
                    ],
                    'label' => Yii::t('cati', 'MH'),
                ], [
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => ['style' => 'text-align: right; padding: 0.375em 2px;'],
                    'label' => '%',
                    'value' => function ($model) {
                        return $model['TOTAL_ALUMNOS'] ? $model['NUMERO_MATRICULA_HONOR'] * 100 / $model['TOTAL_ALUMNOS'] : 0;
                    },
                    'format' => ['decimal', 1],
                ], [
                    'attribute' => 'NUMERO_OTRO',
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => [  // HTML attributes for the header cell tag
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'style' => 'text-align: right; padding: 0.375em 2px;',
                        'title' => Yii::t('cati', 'Otros'),
                    ],
                    'label' => Yii::t('cati', 'Otr'),
                ], [
                    'contentOptions' => ['style' => 'text-align: right; padding: 8px 2px 8px;'],
                    'headerOptions' => ['style' => 'text-align: right; padding: 0.375em 2px;'],
                    'label' => '%',
                    'value' => function ($model) {
                        return $model['TOTAL_ALUMNOS'] ? $model['NUMERO_OTRO'] * 100 / $model['TOTAL_ALUMNOS'] : 0;
                    },
                    'format' => ['decimal', 1],
                ],
            ],
            'dataProvider' => $dpCalificaciones,
            'options' => ['class' => 'cabecera-azul'],
            'summary' => false,
            'tableOptions' => ['class' => 'table table-striped table-hover'],
        ]
    );
    echo '</div>';
endforeach;
