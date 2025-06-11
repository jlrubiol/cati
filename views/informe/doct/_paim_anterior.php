<?php
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

if (!$estudio_anterior) {
    return;
}

# Tablas del PAIM del año anterior, para completarlas.
# En el PDF no se imprimen, sino que se mostrará todo el PAIM como anexo al final.
echo '<div class="noprint">';
foreach ($preguntas_paim as $i => $pregunta) {
    // 1 => <h3>, 1.1 => <h4>, 1.1.1 => <h5>
    $level = count(explode('.', $pregunta->apartado));
    $start = '<h' . ($level + 2) . '>';
    $end = '</h' . ($level + 2) . '>';

    $campos_pregunta = array_map(function ($a) { return trim($a); }, explode(',', $pregunta->atributos));

    echo "{$start}0.0." . HtmlPurifier::process($pregunta->apartado) . '.— '
      . HtmlPurifier::process($pregunta->titulo) . "$end\n";

    if (!isset($respuestas_paim[$pregunta->id])) {
        continue;
    }
    $dataProvider = new ArrayDataProvider(
        [
            'allModels' => $respuestas_paim[$pregunta->id],
            'pagination' => false,  // ['pageSize' => 10],
            # 'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_ASC]],
            'sort' => [
                'attributes' => ['ambito_id', 'necesidad_detectada', 'objetivo'],
                'defaultOrder' => ['ambito_id' => SORT_ASC, 'necesidad_detectada' => SORT_ASC, 'objetivo' => SORT_ASC]
            ],
        ]
    );

    echo "<div class='table-responsive'>\n";
    echo GridView::widget(
        [
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'id',
                'visible' => ($pregunta->atributos != 'fecha'),  // En la sección de «Fecha aprobación CGC» no mostramos el ID.
                # 'attribute' => 'codigo',  # Para seguir el patrón decidido por el Vicerrectorado
            ],
            /* [
                'attribute' => 'ambito',
                'visible' => in_array('ambito_id', $campos_pregunta),
                'value' => function ($respuesta) { return $respuesta->ambito['valor'] ?? null; },
                'headerOptions' => ['class' => 'col-sm-1'],
            ], [
                'attribute' => 'necesidad_detectada',
                'visible' => in_array('necesidad_detectada', $campos_pregunta),
                'headerOptions' => ['class' => 'col-sm-2'],
            ],
            */
            [
                'attribute' => 'objetivo',
                'visible' => in_array('objetivo', $campos_pregunta),
                'headerOptions' => ['class' => 'col-sm-2'],
            ], [
                'attribute' => 'apartado_memoria',
                'visible' => in_array('apartado_memoria_id', $campos_pregunta),
                'value' => function ($respuesta) { return $respuesta->apartadoMemoria['valor'] ?? null; },
                'headerOptions' => ['class' => 'col-sm-1'],
            ], [
                'attribute' => 'tipo_modificacion',
                'visible' => in_array('tipo_modificacion_id', $campos_pregunta),
                'value' => function ($respuesta) { return $respuesta->tipoModificacion['valor'] ?? null; },
                'headerOptions' => ['class' => 'col-sm-1'],
            ], [
                'attribute' => 'titulo',
                'visible' => in_array('titulo', $campos_pregunta),
                'headerOptions' => ['class' => 'col-sm-2'],
            ], [
                'attribute' => 'descripcion_breve',
                'visible' => in_array('descripcion_breve', $campos_pregunta),
                'headerOptions' => ['class' => 'col-sm-4'],
            ], [
                'attribute' => 'descripcion_amplia',
                'visible' => in_array('descripcion_amplia', $campos_pregunta),
                'headerOptions' => ['class' => 'col-sm-4'],
            ], [
                'label' => Yii::t('cati', 'Resp. acción y seguimiento'),
                'attribute' => 'responsable_accion',
                'visible' => in_array('responsable_accion', $campos_pregunta),
                'headerOptions' => ['class' => 'col-sm-2'],
            ], [
                'attribute' => 'problema',
                'visible' => in_array('problema', $campos_pregunta),
            ], [
                'attribute' => 'acciones',
                'visible' => in_array('acciones', $campos_pregunta),
            ], [
                'attribute' => 'inicio',
                'visible' => in_array('inicio', $campos_pregunta),
            ], [
                'attribute' => 'final',
                'visible' => in_array('final', $campos_pregunta),
            ], [
                'attribute' => 'responsable_competente',
                'visible' => in_array('responsable_competente', $campos_pregunta),
                'headerOptions' => ['class' => 'col-sm-2'],
            ],
            /*
            [
                'attribute' => 'responsable_aprobacion',
                'visible' => in_array('responsable_aprobacion_id', $campos_pregunta),
                'value' => function ($respuesta) { return $respuesta->responsableAprobacion['valor'] ?? null; },
            ], [
                'attribute' => 'plazo_implantacion',
                'visible' => in_array('plazo_implantacion', $campos_pregunta),
            ],
            */
            [
                'attribute' => 'plazo',
                'visible' => in_array('plazo_id', $campos_pregunta),
                'value' => function ($respuesta) { return $respuesta->plazo['valor'] ?? null; },
            ],[
                'attribute' => 'indicador',
                'visible' => in_array('indicador', $campos_pregunta),
                'headerOptions' => ['class' => 'col-sm-2'],
            ], [
                'attribute' => 'valores_a_alcanzar',
                'visible' => in_array('valores_a_alcanzar', $campos_pregunta),
                'headerOptions' => ['class' => 'col-sm-2'],
            ], [
                'attribute' => 'valores_alcanzados',
                'visible' => in_array('valores_a_alcanzar', $campos_pregunta),
            ], [
                'attribute' => 'justificacion_breve',
                'visible' => in_array('justificacion_breve', $campos_pregunta),
            ], [
                'attribute' => 'observaciones',
                'headerOptions' => ['class' => 'col-sm-2'],
            ], [
                'attribute' => 'cumplimiento',
                'visible' => in_array('cumplimiento', $campos_pregunta),
            ], [
                'attribute' => 'justificacion',
                'visible' => in_array('justificacion', $campos_pregunta),
                'headerOptions' => ['class' => 'col-sm-2'],
            ], [
                'attribute' => 'nivel',
                'visible' => in_array('nivel', $campos_pregunta),
            ], [
                'attribute' => 'fecha',
                'visible' => in_array('fecha', $campos_pregunta),
            ], [
                'attribute' => 'estado',
                'value' => function ($respuesta) { return $respuesta->estado['valor'] ?? null; },
            ], [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'completar' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('cati', 'Completar la acción'),
                            'aria-label' => Yii::t('cati', 'Completar la acción'),
                            'data-pjax' => '0',
                            'class' => 'text-info',
                        ];

                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            $url,
                            $options
                        );
                    },
                ],
                // 'controller' => '',
                'template' => '{completar}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    $params = [
                        'plan-mejora/' . $action,
                        'estudio_id' => $model->estudio_id,
                        'id' => $model->id,
                    ];

                    return Url::toRoute($params);
                },
                // visibleButtons => ...,
                'contentOptions' => ['nowrap' => 'nowrap', 'class' => 'noprint'],
                # 'visible' => ...,
            ],
        ],
        'options' => ['class' => 'cabecera-azul'],
        'summary' => '', // Do not show `Showing 1-19 of 19 items'.
        'tableOptions' => [
            'class' => 'table table-striped table-hover',
            'id' => "tabla_paim_{$i}",
        ],
        ]
    );
    echo "</div>\n";  # table-responsive
}  # foreach preguntas_paim
echo "<br>";

echo Html::a(
    '<span class="glyphicon glyphicon-eye-open"></span> ' . Yii::t('cati', 'Ver PAIM completado'),
    [
        'plan-mejora/ver',
        'estudio_id' => $estudio_anterior->id,
        'anyo' => $estudio_anterior->anyo_academico,
        'completado' => True,
    ],
    ['id' => "boton-ver-paim", 'class' => 'btn btn-info']
) . "\n";
?>

<br>

</div> <!-- no-print -->
