<?php
/**
 * Fragmento de vista con la tabla de acceso, admisión y matrícula de doctorado agrupada por macroáreas.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use app\models\DoctoradoMacroarea;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

function mostrarTabla($datos, $caption, $descripciones)
{
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
                'contentOptions' => function ($model, $key, $index, $column) use ($descripciones) {
                    return [
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                        'title' => $descripciones[$model[0]],
                    ];
                },
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
        'caption' => sprintf("<p style='font-size: 140%%;'>%s</p>", $caption),
        'captionOptions' => ['style' => 'text-align: center;'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '—'],
        'options' => ['class' => 'cabecera-azul'],
        'summary' => '', // Do not show `Showing 1-19 of 19 items'.
        'tableOptions' => ['class' => 'table table-striped table-hover'],
    ]);
    echo '</div>';
}

# Ver listado numerado en `_personal_academico.php`
# 3 (plazas_ofertadas) - 4 (num_solicitudes)
mostrarTabla(array_slice($datos, 3, 2), Yii::t('cati', 'Oferta y demanda'), $descripciones);
# 5 (alumnos_nuevo_ingreso) - 8 (porc_ni_tiempo_parcial)
mostrarTabla(array_slice($datos, 5, 4), Yii::t('cati', 'Estudiantes de nuevo ingreso'), $descripciones);
# 9 (alumnos_matriculados) - 13 (porc_matri_tiempo_parcial)
mostrarTabla(array_slice($datos, 9, 5), Yii::t('cati', 'Total de estudiantes matriculados'), $descripciones);
