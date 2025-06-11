<?php
/**
 * Fragmento de vista con la tabla de valoración de la satisfacción con la formación recibida.
 * Campos numero_alu_encuesta_global_[1-5] de la tabla DATUZ_doctorado.
 * Esta tabla la actualiza el script `doct2cati.php`, que se puede invocar desde
 *   Gestión -> Doctorado -> Otros -> Actualizar datos académicos de Doctorado
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

// Fila de la cabecera
$cabeceras = array_column($datos, 'ano_academico');
$cabeceras = array_map(function ($anyo) {
    return sprintf('%d/%d', $anyo, $anyo + 1);
}, $cabeceras);
array_unshift($cabeceras, 'indicador');

// Datos
$cod_indicadores = ['numero_alu_encuesta_global_1', 'numero_alu_encuesta_global_2', 'numero_alu_encuesta_global_3',
    'numero_alu_encuesta_global_4', 'numero_alu_encuesta_global_5', ];

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
    'value' => function ($model) use ($doctorado) {
        return $doctorado->getAttributeLabel($model['indicador']);
    },
];
// Definición de las columnas de los años
$anyos = array_slice($cabeceras, 1);
foreach ($anyos as $columna_anyo) {
    if ($columna_anyo >= '2016/2017') {
        $columnas[] = [
            'attribute' => $columna_anyo,
            'contentOptions' => ['style' => 'text-align: right;'],
            'headerOptions' => ['style' => 'text-align: right;'],
            'value' => function ($model, $key, $index, $column) use ($columna_anyo) {
                return $model[$columna_anyo];  // ?: Yii::t('doct', 'Ver texto');
            },
        ];
    }
}

// Tablas
echo "<div class='table-responsive'>";
echo GridView::widget([
    'columns' => $columnas,
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $datos_anualizados,
        'pagination' => false,
    ]),
    'options' => ['class' => 'cabecera-azul'],
    'summary' => false,
    'tableOptions' => ['class' => 'table table-striped table-hover'],
    // 'caption' => '',
    'captionOptions' => ['style' => 'text-align: center;'],
]);
echo '</div>';
