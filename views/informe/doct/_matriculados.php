<?php
/**
 * Fragmento de vista con la tabla de estudiantes de nuevo ingreso de doctorado.
 * Se usa para la web (Indicadores y resultados de encuestas -> Indicadores -> Matrícula).
 * Para los PDF de los IEC se usa `acceso_admision_matricula`.
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
$cabeceras = array_map(
    function ($anyo) {
        return sprintf('%d/%d', $anyo, $anyo + 1);
    }, $cabeceras
);
array_unshift($cabeceras, 'indicador');

// Datos
$cod_indicadores = ['alumnos_matriculados', 'porc_matr_extrajeros', 'porc_alumnos_beca', 'porc_alumnos_beca_distinta', 'porc_matri_tiempo_parcial'];
$datos_volteados = [];
foreach ($cod_indicadores as $cod_indicador) {
    // $fila = array_column($datos, $cod_indicador);  // array_column no devuelve no incluye los elementos NULL
    $fila = array_map(
        function ($o) use ($cod_indicador) {
            return $o->{$cod_indicador};
        }, $datos
    );
    array_unshift($fila, $cod_indicador);
    $datos_volteados[] = array_combine($cabeceras, $fila);
}

$doctorado = new app\models\Doctorado();

// Definición de la columna del indicador
$columnas[] = [
    'attribute' => 'indicador',
    'contentOptions' => function ($model) use ($descripciones) {
        return [
            'title' => $descripciones[$model['indicador']],
        ];
    },
    'header' => Yii::t('doct', 'Indicador'),
    'value' => function ($model) use ($doctorado) {
        return $doctorado->getAttributeLabel($model['indicador']);
    },
];
// Definición de las columnas de los años
$anyos = array_slice($cabeceras, 1);
foreach ($anyos as $anyo) {
    $columnas[] = [
        'attribute' => $anyo,
        'contentOptions' => ['style' => 'text-align: right;'],
        'headerOptions' => ['style' => 'text-align: right;'],
        'value' => function ($model, $key, $index, $column) use ($anyo) {
            return $model[$anyo];  // ?: Yii::t('doct', 'Ver texto');
        },
    ];
}

// Tabla
echo "<div class='table-responsive'>";
echo GridView::widget(
    [
    'columns' => $columnas,
    'dataProvider' => new ArrayDataProvider(
        [
        'allModels' => $datos_volteados,
        'pagination' => false,
        ]
    ),
    'options' => ['class' => 'cabecera-azul'],
    'summary' => false,
    'tableOptions' => ['class' => 'table table-striped table-hover'],
    'caption' => sprintf("<p style='font-size: 140%%;'>%s</p>", "Estudiantes matriculados"),
    'captionOptions' => ['style' => 'text-align: center;'],
    ]
);
echo '</div>';
