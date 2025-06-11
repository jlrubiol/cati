<?php
/**
 * Fragmento de vista con la tabla de número de programas de doctorado agrupada por macroáreas.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use app\models\DoctoradoMacroarea;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

$ramas = array_slice($datos[1], 2, 6);
// 51: num_programas (ver listado en `_personal_academico.php`)
$datos = array_slice($datos, 51, 1);
$caption = Yii::t('cati', 'Número de programas por macroárea');

$dataProvider = new ArrayDataProvider([
    'allModels' => $datos,
    'pagination' => false,  // ['pageSize' => 10],
]);

$model = new DoctoradoMacroarea();

echo "<div class='table-responsive'>";
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 1,
            'label' => Yii::t('cati', 'Concepto'),
            'value' => function ($registro) use ($model) {
                return '<strong>'.$model->getAttributeLabel($registro[1]).'</strong>';
            },
            'format' => 'html',
            /*
            'contentOptions' => function ($model, $key, $index, $column) use ($descripciones) {
                return [
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'left',
                    'title' => $descripciones[$model[0]],
                ];
            },
            */
        ], [
            'attribute' => 2,
            'contentOptions' => ['style' => 'text-align: right;'],
            'format' => ['decimal'],
            'headerOptions' => ['style' => 'text-align: right;'],
            'label' => Yii::t('cati', 'Total'),
        ], [
            'attribute' => 3,
            'contentOptions' => ['style' => 'text-align: right;'],
            'format' => ['decimal'],
            'headerOptions' => ['style' => 'text-align: right;'],
            'label' => Yii::t('cati', 'Artes y Humanidades'),
        ], [
            'attribute' => 4,
            'contentOptions' => ['style' => 'text-align: right;'],
            'format' => ['decimal'],
            'headerOptions' => ['style' => 'text-align: right;'],
            'label' => Yii::t('cati', 'Ciencias Sociales y Jurídicas'),
        ], [
            'attribute' => 5,
            'contentOptions' => ['style' => 'text-align: right;'],
            'format' => ['decimal'],
            'headerOptions' => ['style' => 'text-align: right;'],
            'label' => Yii::t('cati', 'Ciencias de la Salud'),
        ], [
            'attribute' => 6,
            'contentOptions' => ['style' => 'text-align: right;'],
            'format' => ['decimal'],
            'headerOptions' => ['style' => 'text-align: right;'],
            'label' => Yii::t('cati', 'Ingeniería y Arquitectura'),
        ], [
            'attribute' => 7,
            'contentOptions' => ['style' => 'text-align: right;'],
            'format' => ['decimal'],
            'headerOptions' => ['style' => 'text-align: right;'],
            'label' => Yii::t('cati', 'Ciencias'),
        ],
    ],
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '—', ],
    'options' => ['class' => 'cabecera-azul'],
    'summary' => false, // Do not show `Showing 1-19 of 19 items'.
    'tableOptions' => ['class' => 'table table-striped table-hover'],
    'caption' => sprintf("<p style='font-size: 140%%;'>%s</p>", $caption),
    'captionOptions' => ['style' => 'text-align: center;'],
]);
echo '</div>';
