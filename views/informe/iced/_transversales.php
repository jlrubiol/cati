<?php
/**
 * Fragmento de vista con la tabla de resultados de actividades transversales agrupada por macroáreas.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */

use app\models\DoctoradoMacroarea;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


# 14 (alumnos_act_transv) - 15 (cursos_act_transv)
# Ver listado en `_personal_academico.php`
$modelos = array_slice($datos, 14, 2);
for ($i = 2; $i <= 8; $i++) {
    $modelos[0][$i] = sprintf(
        '%s/%d',
        isset($modelos[0][$i]) ? $modelos[0][$i] : '?',
        isset($datos[9][$i]) ? $datos[9][$i] : '—'  // alumnos_matriculados
    );
}

$ramas = array_slice($datos[1], 2, 6);  // cod_rama_conocimiento
if ($mostrar_botones) {
    $botones = ['', ''];
    foreach ($ramas as $rama_id) {
        $botones[] = Html::a(
            '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('gestion', 'Editar datos'),
            ['doctorado-macroarea/editar-datos', 'anyo' => $anyo, 'rama_id' => $rama_id],
            [
                'id' => "editar-datos-{$rama_id}",
                'class' => 'btn btn-info btn-xs',  // Button
                'title' => Yii::t('gestion', 'Editar los datos'),
            ]
        );
    }

    array_push($modelos, $botones);
}

$dataProvider = new ArrayDataProvider(
    [
        'allModels' => $modelos,
        'pagination' => false,  // ['pageSize' => 10],
    ]
);

$model = new DoctoradoMacroarea();

echo "<div class='table-responsive'>";
echo GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 1,
                'label' => Yii::t('cati', 'Concepto'),
                'value' => function ($registro) use ($model) {
                    return '<strong>' . $model->getAttributeLabel($registro[1]) . '</strong>';
                },
                'format' => 'html',
                'contentOptions' => function ($model, $key, $index, $column) use ($descripciones) {
                    return [
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                        'title' => ArrayHelper::getValue($descripciones, $model[0]),
                    ];
                },
            ], [
                'attribute' => 2,
                'contentOptions' => ['style' => 'text-align: right;'],
                'format' => 'html',  // decimal',
                'headerOptions' => ['style' => 'text-align: right;'],
                'label' => Yii::t('cati', 'Total'),
            ], [
                'attribute' => 3,
                'contentOptions' => ['style' => 'text-align: right;'],
                'format' => 'html',  // decimal',
                'headerOptions' => ['style' => 'text-align: right;'],
                'label' => Yii::t('cati', 'Artes y Humanidades'),
            ], [
                'attribute' => 4,
                'contentOptions' => ['style' => 'text-align: right;'],
                'format' => 'html',  // decimal',
                'headerOptions' => ['style' => 'text-align: right;'],
                'label' => Yii::t('cati', 'Ciencias Sociales y Jurídicas'),
            ], [
                'attribute' => 5,
                'contentOptions' => ['style' => 'text-align: right;'],
                'format' => 'html',  // decimal',
                'headerOptions' => ['style' => 'text-align: right;'],
                'label' => Yii::t('cati', 'Ciencias de la Salud'),
            ], [
                'attribute' => 6,
                'contentOptions' => ['style' => 'text-align: right;'],
                'format' => 'html',  // decimal',
                'headerOptions' => ['style' => 'text-align: right;'],
                'label' => Yii::t('cati', 'Ingeniería y Arquitectura'),
            ], [
                'attribute' => 7,
                'contentOptions' => ['style' => 'text-align: right;'],
                'format' => 'html',  // decimal',
                'headerOptions' => ['style' => 'text-align: right;'],
                'label' => Yii::t('cati', 'Ciencias'),
            ],
        ],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '—'],
        'options' => ['class' => 'cabecera-azul'],
        'summary' => false,  // Do not show `Showing 1-19 of 19 items'.
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        // 'caption' => sprintf("<p style='font-size: 140%%;'>%s</p>", $caption),
        // 'captionOptions' => ['style' => 'text-align: center;'],
    ]
);
echo '</div>';
