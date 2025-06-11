<?php

use yii\helpers\Html;
use app\models\Profesorado;

if (!$evoluciones) {
    return;
}

// Si la vista está incluida desde informe/ver, estas variables no están definidas, pero `$estudio` sí.
if (!isset($nombre_estudio)) {
    $nombre_estudio = $estudio->nombre;
}

$subnum_tabla = 0;
foreach ($evoluciones as $nombre_centro => $evolucion) {
    $claves = array_keys($evolucion[0]);
    $anyos = array_filter($claves, 'is_int');
    $subnum_tabla++;
    $titulo = (isset($apartado) ? "Tabla {$apartado}.{$num_tabla}.{$subnum_tabla}: " : '' ) . Yii::t('cati', 'Evolución del profesorado');

    $caption = sprintf(
        "<p style='font-size: 140%%;'>%s</p>\n",
        $titulo,
        Html::encode($nombre_estudio)
    );
    $caption .= sprintf(
        '<b>%s:</b> %s<br>',
        Yii::t('cati', 'Estudio'),
        Html::encode($nombre_estudio)
    );
    $caption .= sprintf(
        "<p><b>%s:</b> %s</p>\n",
        Yii::t('cati', 'Centro'),
        Html::encode($nombre_centro)
    );

    \yii\widgets\Pjax::begin([
        'id' => 'pjax-main',
        'enableReplaceState' => false,
        'linkSelector' => '#pjax-main ul.pagination a, th a',
        // 'clientOptions' => ['pjax:success' => 'function() { alert("yo"); }'],
    ]); ?>

    <div class="table-responsive">
        <?php echo yii\grid\GridView::widget([
            'dataProvider' => new yii\data\ArrayDataProvider(
                [
                    'allModels' => $evolucion,
                    // 'sort' => ['attributes' => []],
                ]
            ),
            'columns' => [
                [
                    'attribute' => 'categoria',
                    'label' => Yii::t('cati', 'Categoría'),
                    'value' => function ($p) {
                        return Yii::t('db', $p['categoria']);
                    },
                ], [
                    'attribute' => $anyos[0],
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'headerOptions' => ['style' => 'text-align: right;'],
                ], [
                    'attribute' => $anyos[1],
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'headerOptions' => ['style' => 'text-align: right;'],
                ], [
                    'attribute' => $anyos[2],
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'headerOptions' => ['style' => 'text-align: right;'],
                ], [
                    'attribute' => $anyos[3],
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'headerOptions' => ['style' => 'text-align: right;'],
                ], [
                    'attribute' => $anyos[4],
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'headerOptions' => ['style' => 'text-align: right;'],
                ], [
                    'attribute' => $anyos[5],
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'headerOptions' => ['style' => 'text-align: right;'],
                ], [
                    'attribute' => $anyos[6],
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'headerOptions' => ['style' => 'text-align: right;'],
                ],
            ],
            'caption' => $caption,
            'captionOptions' => ['style' => 'text-align: center;'],
            'footerRowOptions' => ['style' => 'text-align: right;'],
            'options' => ['class' => 'cabecera-azul'],
            // 'showFooter' => true,
            'summary' => false,
            'tableOptions' => ['class' => 'table table-bordered table-striped table-hover'],
        ]); ?>
    </div>

    <?php
    \yii\widgets\Pjax::end();
    echo "<br>\n";
}
