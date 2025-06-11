<?php
use yii\helpers\Html;
use app\models\Plan;
use app\models\Profesorado;

if (!$estructuras) {
    return;
}

// Si la vista está incluida desde informe/ver, estas variables no están definidas, pero `$estudio` sí.
if (!isset($nombre_estudio)) {
    $nombre_estudio = $estudio->nombre;
}

$subnum_tabla = 0;
foreach ($estructuras as $nombre_centro => $profesorado_del_centro) {
    $fecha = date('d-m-Y', strtotime($profesorado_del_centro[0]['fecha_carga']));
    $subnum_tabla++;
    $titulo = (isset($apartado) ? "Tabla {$apartado}.{$num_tabla}.{$subnum_tabla}: " : '' ) . Yii::t('cati', 'Tabla de estructura del profesorado');
    $caption = sprintf(
        "<p style='font-size: 140%%;'>%s</p>\n",
        $titulo,
        Html::encode($nombre_estudio)
    );
    $caption .= sprintf(
        "<p style='font-size: 120%%;'>%s: %d/%d</p>\n",
        Yii::t('cati', 'Año académico'),
        $anyo,
        $anyo + 1
    );
    $caption .= sprintf(
        '<b>%s:</b> %s<br>',
        Yii::t('cati', 'Estudio'),
        Html::encode($nombre_estudio)
    );
    $caption .= sprintf(
        "<p><b>%s:</b> %s<br>\n<b>%s:</b> %s\n</p>\n",
        Yii::t('cati', 'Centro'),
        Html::encode($nombre_centro),
        Yii::t('cati', 'Datos a fecha'),
        $fecha
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
                    'allModels' => $profesorado_del_centro,
                    'sort' => [
                        'attributes' => [
                            'categoria', 'num_profesores', 'num_en_primer_curso',
                            'num_sexenios', 'num_quinquenios', 'horas_impartidas'
                        ],
                    ],
                ]
            ),
            'columns' => [
                [
                    'attribute' => 'categoria',
                    'footer' => "<div style='text-align: left;'>" . Yii::t('cati', 'Total personal académico') . '</div>',
                    'value' => function ($p) {
                        return Yii::t('db', $p->categoria);
                    },
                ], [
                    'attribute' => 'num_profesores',
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'headerOptions' => ['style' => 'text-align: right;'],
                    'footer' => Profesorado::getTotal($profesorado_del_centro, 'num_profesores'),
                    'label' => Yii::t('cati', 'Total'),
                ], [
                    'attribute' => 'porcentaje_profesor',
                    'contentOptions' => ['style' => 'text-align: right;'],
                    # Ponemos 100% para evitar que la suma sea distinta a 100,00 por errores de redondeo.
                    # Solicitado por Fernando López Plana el 2023-07-19
                    'footer' => Yii::$app->formatter->asDecimal(100, 2),  # Yii::$app->formatter->asDecimal(Profesorado::getTotal($profesorado_del_centro, 'porcentaje_profesor'), 2),
                    'format' => ['decimal', 2],
                    'headerOptions' => ['style' => 'text-align: right;'],
                    'label' => '%',
                ], [
                    'attribute' => 'num_en_primer_curso',
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'footer' => Profesorado::getTotal($profesorado_del_centro, 'num_en_primer_curso'),
                    'headerOptions' => ['style' => 'text-align: right;'],
                ], [
                    'attribute' => 'num_sexenios',
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'footer' => Profesorado::getTotal($profesorado_del_centro, 'num_sexenios'),
                    'headerOptions' => ['style' => 'text-align: right;'],
                ], [
                    'attribute' => 'num_quinquenios',
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'footer' => Profesorado::getTotal($profesorado_del_centro, 'num_quinquenios'),
                    'headerOptions' => ['style' => 'text-align: right;'],
                ], [
                    'attribute' => 'horas_impartidas',
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'footer' => Yii::$app->formatter->asDecimal(Profesorado::getTotal($profesorado_del_centro, 'horas_impartidas'), 1),
                    'format' => ['decimal', 1],
                    'headerOptions' => ['style' => 'text-align: right;'],
                ], [
                    'attribute' => 'porcentaje_horas',
                    'label' => '%',
                    'footer' => Yii::$app->formatter->asDecimal(Profesorado::getTotal($profesorado_del_centro, 'porcentaje_horas'), 2),
                    'format' => ['decimal', 2],
                    'contentOptions' => ['style' => 'text-align: right;'],
                    'headerOptions' => ['style' => 'text-align: right;'],
                ],
            ],
            'caption' => $caption,
            'captionOptions' => ['style' => 'text-align: center;'],
            'footerRowOptions' => ['style' => 'text-align: right;'],
            'options' => ['class' => 'cabecera-azul'],
            'showFooter' => true,
            'summary' => false,
            'tableOptions' => ['class' => 'table table-bordered table-striped table-hover'],
        ]); ?>
    </div>

    <?php
    \yii\widgets\Pjax::end();
    echo "<br>\n";
}
