<?php
/**
 * Vista de las asignaturas de un itinerario de un estudio.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

$this->title = sprintf(
    Yii::t('cati', '%s (curso %d-%d)'),
    Html::encode($nombre_itinerario),
    $anyo_academico,
    $anyo_academico + 1
);
$this->registerMetaTag(
    [
        'name' => 'description',
        'content' => sprintf(
            Yii::t('cati', 'Listado de las asignaturas del itinerario %s de %s (%s, plan %s)'),
            Html::encode($nombre_itinerario),
            Html::encode($estudio->nombre),
            Html::encode($centro->nombre),
            $plan->id_nk
        ),
    ]
);
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico]
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('cati', 'Asignaturas del plan ') . $plan->id_nk,
    'url' => [
        'estudio/asignaturas',
        'anyo_academico' => $anyo_academico,
        'estudio_id' => $estudio->id,
        'centro_id' => $centro->id,
        'plan_id_nk' => $plan->id_nk,
    ],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?php echo Html::encode($estudio->nombre); ?></h1>
<h2><?php echo Html::encode($centro->nombre); ?></h2>
<h3><?php echo Html::encode($nombre_itinerario); ?></h3>
<h4><?php printf(Yii::t('cati', 'Curso %d-%d'), $anyo_academico, $anyo_academico + 1); ?></h4>

<hr><br>


<div class="row breadcrumb">
    <?php
    if ($plan->notas) {
        ?>
        <h3><?php echo Yii::t('cati', 'Notas del plan'); ?></h3>

        <p><?php echo HtmlPurifier::process($plan->notas); ?></p><br>
        <?php
    } ?>
</div>

<?php

if ($itinerarios) {
    // Los grados tienen `menciones' y los másteres `especialidades'.
    if ($estudio->esGrado()) {
        printf("<h3>%s</h3>\n", Yii::t('cati', 'Menciones'));
        if ($plan->compatible_men_esp) {
            printf("<p>%s</p>\n", Yii::t('cati', 'Permite compatibilizar menciones.'));
        }
    } else {
        printf("<h3>%s</h3>\n", Yii::t('cati', 'Especialidades'));
        if ($plan->compatible_men_esp) {
            printf("<p>%s</p>\n", Yii::t('cati', 'Permite compatibilizar especialidades.'));
        }
    }
    echo "<ul class='itinerarios'>\n";
    foreach ($itinerarios as $itinerario) {
        # Las asignaturas de los itinerarios proceden de la tabla `ODS_ASIG_EST_CENT_PLAN_ITINER`
        # El nombre de los itinerarios procede de `ODS_ITINERARIO_DESCRIPCION`
        echo '<li>';
        echo Html::a(
            Html::encode($itinerario['descripcion']),
            [
                'estudio/asignaturas-itinerario',
                'anyo_academico' => $anyo_academico,
                'estudio_id' => $estudio->id,
                'centro_id' => $centro->id,
                'plan_id_nk' => $plan->id_nk,
                'itinerario_id_nk' => $itinerario['id_nk'],
            ]
        );
        echo "</li>\n";
    }
    echo "</ul>\n";
}

$dataProvider = new ArrayDataProvider(
    [
        'allModels' => $asignaturas,
        'pagination' => false,  // ['pageSize' => 10],
        'sort' => ['attributes' => ['curso', 'asignatura_id', 'descripcion']],
    ]
);

\yii\widgets\Pjax::begin(
    [
        'id' => 'pjax-main',
        'enableReplaceState' => false,
        'linkSelector' => '#pjax-main ul.pagination a, th a',
        // 'clientOptions' => ['pjax:success' => 'function() { alert("yo"); }'],
    ]
);

echo "<div class='table-responsive'>";
echo GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'curso',
            ], [
                'label' => 'Periodo',
                'value' => function ($asignatura) {
                    $tip = str_replace('S', Yii::t('cati', 'Semestre '), $asignatura['periodo']);
                    $tip = str_replace('C', Yii::t('cati', 'Cuatrimestre '), $tip);
                    $tip = str_replace('A', Yii::t('cati', 'Anual'), $tip);

                    return "<div class='tooltip'>" . $asignatura['periodo']
                        . "<span class='tooltiptext'>$tip</span></div>";
                },
                'format' => 'html',
            ], [
                'attribute' => 'asignatura_id',
                'label' => Yii::t('cati', 'Código'),
                'value' => function ($asignatura) use ($estudio, $centro, $plan) {
                    return Html::a(
                        $asignatura['asignatura_id'],
                        [
                            'estudio/asignatura',
                            'anyo_academico' => $asignatura['anyo_academico'],
                            'asignatura_id' => $asignatura['asignatura_id'],
                            'estudio_id' => $estudio->id,
                            'centro_id' => $centro->id,
                            'plan_id_nk' => $plan->id_nk,
                        ]
                    );
                },
                'format' => 'html',
            ], [
                'attribute' => 'descripcion',
                'label' => Yii::t('cati', 'Nombre'),
                'value' => function ($asignatura) use ($estudio, $centro, $plan) {
                    return Html::a(
                        $asignatura['descripcion'],
                        [
                            'estudio/asignatura',
                            'anyo_academico' => $asignatura['anyo_academico'],
                            'asignatura_id' => $asignatura['asignatura_id'],
                            'estudio_id' => $estudio->id,
                            'centro_id' => $centro->id,
                            'plan_id_nk' => $plan->id_nk,
                        ]
                    );
                },
                'format' => 'html',
            ], [
                'label' => Yii::t('cati', 'Carácter'),
                'value' => function ($asignatura) {
                    return Yii::t('clase', $asignatura['clase']);
                },
            ], [
                'attribute' => 'creditos',
                'label' => Yii::t('cati', 'Créditos'),
                'format' => ['decimal', 1],
            ], [
                'attribute' => 'situacion',
                'label' => Yii::t('cati', ''),
                'value' => function ($asignatura) {
                    return ('Ofertada' != $asignatura['situacion']) ? $asignatura['situacion'] : '';
                },
            ],  [
                'label' => Yii::t('cati', 'Lím. plazas opt'),
                'value' => function ($asignatura) {
                    return $asignatura['numero_plazas'] ? $asignatura['numero_plazas'] : '-';
                },
            ], [
                'attribute' => 'idioma',
                'format' => 'html',
            ],
        ],
        'options' => ['class' => 'cabecera-azul'],
        'summary' => '', // Do not show `Showing 1-19 of 19 items'.
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
    ]
);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end();
