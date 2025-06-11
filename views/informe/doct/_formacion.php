<?php
/**
 * Fragmento de vista con la tabla de Resultados de aprendizaje.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

// Fila de la cabecera
$descripciones['num_medio_resultados_tesis2'] = $descripciones['num_medio_resultados_tesis'];
$cabeceras = array_column($datos, 'ano_academico');
$cabeceras = array_map(function ($anyo) {
    # return sprintf('%d/%d', $anyo, $anyo + 1);
    return sprintf('%d/%d', $anyo%100, ($anyo + 1)%100);
}, $cabeceras);
array_unshift($cabeceras, 'indicador');

// Datos
$cod_indicadores = ['numero_tesis_tiempo_completo', 'numero_tesis_tiempo_parcial', 'duracion_media_tiempo_completo',
    'duracion_media_tiempo_parcial', 'porc_abandono', 'porc_tesis_no_pri_prorroga', 'porc_tesis_no_seg_prorroga',
    'porc_tesis_cum_laude', 'porc_tesis_men_internacional', 'porc_tesis_men_doc_industrial',
    'porc_tesis_cotutela', 'num_medio_resultados_tesis2'];
if (\Yii::$app->user->can('editarInforme', ['estudio' => $estudio])) {
    $cod_indicadores[] = 'id';  // ID de la columna en la tabla DATUZ_doctorado
}
foreach ($cod_indicadores as $cod_indicador) {
    // $fila = array_column($datos, $cod_indicador);  // array_column no devuelve no incluye los elementos NULL
    $fila = array_map(function ($o) use ($cod_indicador) {
        return $o->{$cod_indicador};
    }, $datos);
    array_unshift($fila, $cod_indicador);
    $datos_anualizados[] = array_combine($cabeceras, $fila);
}
$doctorado = new app\models\Doctorado();

// Definición de la columna del indicador
$columnas[] = [
    'attribute' => 'indicador',
    'contentOptions' => function ($model) use ($descripciones) {
        return [
            'title' => isset($descripciones[$model['indicador']]) ? $descripciones[$model['indicador']] : '',
        ];
    },
    'header' => Yii::t('doct', 'Indicador'),
    # 'headerOptions' => ['class' => 'col-sm-3'],
    'value' => function ($model) use ($doctorado) {
        if ($model['indicador'] === 'id') {
            return '';
        }
        return $doctorado->getAttributeLabel($model['indicador']);
    },
];
// Definición de las columnas de los años
$anyos = array_slice($cabeceras, 1);
foreach ($anyos as $columna_anyo) {
    $columnas[] = [
        'attribute' => $columna_anyo,
        'contentOptions' => ['style' => 'text-align: right;'],
        'format' => 'html',
        'headerOptions' => ['style' => 'text-align: right;'],
        'value' => function ($model, $key, $index, $column) use ($anyo, $columna_anyo) {
            if ($model['indicador'] === 'id') {
                return Html::a(
                    '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('gestion', 'Editar'),
                    ['doctorado/editar-datos', 'id' => $model[$columna_anyo], 'anyo' => $anyo],
                    [
                        'id' => 'editar-datos',
                        'class' => 'btn btn-info btn-xs',
                        'title' => Yii::t('gestion', 'Editar los datos del doctorado')
                    ] // Button
                );
            };
            return $model[$columna_anyo];  // ?: Yii::t('doct', 'Ver texto');
        },
    ];
}

// Tabla
$titulo = (isset($apartado) ? "Tabla {$apartado}: " : '' ) . $titulo;
$caption = <<< CAPTION
<div style='text-align: center; color: #777;'>
    <p style='font-size: 140%;'>$titulo</p>
</div>
CAPTION;

echo "<div class='table-responsive'>";
echo GridView::widget([
    'caption' => $caption,
    'columns' => $columnas,
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $datos_anualizados,
        'pagination' => false,
    ]),
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '—', ],
    'options' => ['class' => 'cabecera-azul'],
    'summary' => false,
    'tableOptions' => ['class' => 'table table-striped table-hover'],
]);
echo '</div>';
