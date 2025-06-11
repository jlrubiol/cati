<?php

use app\models\Centro;
use yii\grid\GridView;
use yii\helpers\Html;

$movilidades = $dpMovilidades->getModels();

// Si no hay datos no se puede obtener la fecha de carga para el caption.
if (!$movilidades) {
    return;
}

$anyo = $movilidades[0]->ANO_ACADEMICO;
$fecha_carga = $movilidades[0]->FECHA_CARGA;
$caption = "<div style='text-align: center; color: #777;'>\n";
$caption .= sprintf("  <p style='font-size: 140%%;'>%s</p>\n", Yii::t('cati', 'Estudiantes en planes de movilidad'));
$caption .= sprintf("  <p style='font-size: 120%%;'>%s: %d/%d</p>\n", Yii::t('cati', 'Año académico'), $anyo, $anyo + 1);
$caption .= sprintf("  <p><b>%s:</b> %s<br>\n", Yii::t('cati', 'Titulación'), Html::encode($estudio->nombre));
$caption .= sprintf("    <b>%s:</b> %s</p>\n", Yii::t('cati', 'Datos a fecha'), date('d-m-Y', strtotime($fecha_carga)));
$caption .= "</div>\n";
?>

<div class="table-responsive">
    <?php echo GridView::widget([
        'dataProvider' => $dpMovilidades,
        'columns' => [
            [
                'attribute' => 'COD_CENTRO',
                'label' => Yii::t('models', 'Centro'),
                'value' => function ($model) {
                    return Centro::findOne($model->COD_CENTRO)->nombre;
                },
            ], [
                'attribute' => 'ALUMNOS_MOVILIDAD_SALIDA',
                'contentOptions' => ['style' => 'text-align: right;'],
                'headerOptions' => ['style' => 'text-align: right;'],
            ], [
                'attribute' => 'ALUMNOS_MOVILIDAD_ENTRADA',
                'contentOptions' => ['style' => 'text-align: right;'],
                'headerOptions' => ['style' => 'text-align: right;'],
            ],
        ],
        'caption' => $caption,
        'options' => ['class' => 'cabecera-azul'],
        'summary' => false,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
    ]); ?>
</div>
