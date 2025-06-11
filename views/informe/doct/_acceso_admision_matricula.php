<?php
/**
 * Fragmento de vista con la tabla de acceso, admisión y matrícula de doctorado.
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
$cabeceras = array_column($datos, 'ano_academico');
$cabeceras = array_map(function ($anyo) {
    return sprintf('%d/%d', $anyo, $anyo + 1);
}, $cabeceras);
array_unshift($cabeceras, 'indicador');

// Datos
$tablas = [
    'Oferta y demanda' => ['plazas_ofertadas', 'num_solicitudes'],
    'Estudiantes de nuevo ingreso' => ['alumnos_nuevo_ingreso', 'porc_est_previo_nouz', 'porc_ni_comp_formacion', 'porc_ni_tiempo_parcial'],
    'Total de estudiantes matriculados' => ['alumnos_matriculados', 'porc_matr_extrajeros', 'porc_alumnos_beca', 'porc_alumnos_beca_distinta', 'porc_matri_tiempo_parcial'],
];

foreach ($tablas as $caption => $cod_indicadores) {
    $datos_volteados = [];
    if (\Yii::$app->user->can('editarInforme', ['estudio' => $estudio])
        && ($caption == 'Total de estudiantes matriculados')
    ) {
        $cod_indicadores[] = 'id';  // ID de la columna en la tabla DATUZ_doctorado
    }

    foreach ($cod_indicadores as $cod_indicador) {
        // $fila = array_column($datos, $cod_indicador);  // array_column no devuelve no incluye los elementos NULL
        $fila = array_map(function ($o) use ($cod_indicador) {
            return $o->{$cod_indicador};
        }, $datos);
        array_unshift($fila, $cod_indicador);
        $datos_volteados[] = array_combine($cabeceras, $fila);
    }
    $datos_anualizados[$caption] = $datos_volteados;
}

$doctorado = new app\models\Doctorado();

// Definición de la columna del indicador
$columnas[] = [
    'attribute' => 'indicador',
    'contentOptions' => function ($model) use ($descripciones) {
        return [
            'title' => $descripciones[$model['indicador']] ?? '',
        ];
    },
    'header' => Yii::t('doct', 'Indicador'),
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
                    '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('gestion', 'Editar datos'),
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

// Tablas
$num_tabla = 0;
foreach ($datos_anualizados as $caption => $datos_volteados) {
    $num_tabla++;
    $caption = (isset($apartado) ? "Tabla {$apartado}.{$num_tabla}: " : '' ) . $caption;
    echo "<div class='table-responsive'>";
    echo GridView::widget([
        'caption' => sprintf("<p style='font-size: 140%%;'>%s</p>", $caption),
        'captionOptions' => ['style' => 'text-align: center;'],
        'columns' => $columnas,
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $datos_volteados,
            'pagination' => false,
        ]),
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '—', ],
        'layout' => "{items}\n{pager}\n{summary}",  // Mostrar el summary bajo la tabla
        'options' => ['class' => 'cabecera-azul'],
        'summary' => ($caption == 'Total de estudiantes matriculados')
                     ? 'Nota: El indicador 1.9 contempla las becas del Vicerrectorado de Investigación, Ayudas a la tutela académica de Doctorado, P.I.F: Contratados predoctorales FPI, FPU y DGA. Aportar en su caso, el número de estudiantes con otras becas: doctorados industriales, las resultantes de la acción Marie Slodowska Curie en sus diversas modalidades (ITN, IF), etc., rellenando los datos en el indicador 1.9.b.'
                     : false,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
    ]);
    echo '</div>';
}
