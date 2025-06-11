<?php
/**
 * Fragmento de vista con la tabla de Personal académico (profesorado. Directores y tutores de tesis).
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

$cabeceras = array_column($datos, 'ano_academico');
$cabeceras = array_map(function ($anyo) {
    return sprintf('%d/%d', $anyo, $anyo + 1);
}, $cabeceras);
array_unshift($cabeceras, 'indicador');
$cod_indicadores = [
    'numero_profesores', // 4.1
    'numero_profesores_uz', // 4.1.1
    'numero_profesores_nouz',  // 4.1.2
    'num_sexenios_profesores',  // 4.2
    'porc_sexenios_vivos',  // 4.3
    'porc_prof_tiempo_completo',  // 4.4
    'porc_expertos_internac_trib',  // 4.5
    'numero_directores_tesis_leidas',  // 4.6
    'porc_dir_tes_le_sexenios_vivos',  // 4.7
    'numero_proy_inter_vivos',  // 4.8
    'numero_proy_nac_vivos',  // 4.9
    'numero_redondeado_publ_indexadas',  // 4.10
    'numero_redondeado_publ_no_indexadas', // 4.11
];
if (\Yii::$app->user->can('editarInforme', ['estudio' => $estudio])) {
    $cod_indicadores[] = 'id';
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
$columnas[] = [
    'attribute' => 'indicador',
    'contentOptions' => function ($model) use ($descripciones) {
        return [
            'title' => isset($descripciones[$model['indicador']]) ? $descripciones[$model['indicador']] : '',
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
                        'class' => 'btn btn-info btn-xs',  // Button
                        'title' => Yii::t('gestion', 'Editar los datos del doctorado')
                    ]
                );
            };
            return $model[$columna_anyo];  // ?: Yii::t('doct', 'Ver texto');
        },
    ];
}

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

echo "<p>Nota indicadores 4.10 y 4.11: Hasta campaña 2023 el período considerado es desde septiembre a agosto; a partir de la campaña 2024 (curso 2023/2024) la información se refiere al año natural de inicio del curso académico objeto del informe.</p>";
