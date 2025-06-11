<?php
/**
 * Vista de las asignaturas de un estudio.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use app\models\Calendario;
use app\models\Estudio;
use app\models\AsignaturaSearch;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

// XXX SMELLS Hasta el curso 2019-20 para el estudio 659 se creaban planes para cada especialidad.
// Le damos a cada 'plan' el nombre de la especialidad en vez de mostrar su número.
if (659 == $estudio->id_nk and $estudio->anyo_academico < 2019) {
    include_once '_nombres_659.php';
    $nombre = \yii\helpers\ArrayHelper::getValue($nombres, $plan->id_nk, "Desconocido {$plan->id_nk}");
    $this->title = sprintf(
        Yii::t('cati', 'Asignaturas de %s (curso %d-%d)'),
        $nombre,
        $anyo_academico,
        $anyo_academico + 1
    );
} else {
    $this->title = sprintf(
        Yii::t('cati', 'Asignaturas del plan %d (curso %d-%d)'),
        $plan->id_nk,
        $anyo_academico,
        $anyo_academico + 1
    );
}
$this->registerMetaTag(
    [
        'name' => 'description',
        'content' => sprintf(
            Yii::t('cati', 'Listado de las asignaturas de %s (%s, plan %s)'),
            Html::encode($estudio->nombre),
            Html::encode($centros[0]->nombre),
            $plan->id_nk
        ),
    ]
);
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $anyo_academico],
];
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?php echo Html::encode($estudio->nombre); ?></h1>
<?php
foreach ($centros as $centro) {
    printf("<h2>%s</h2>\n", Html::encode($centro->nombre));
}

if (659 == $estudio->id_nk and $estudio->anyo_academico < 2019) {
    echo "<h3>$nombre</h3>";
} else {
    printf('<h3>%s %d</h3>', Yii::t('cati', 'Plan'), $plan->id_nk);
}
?>
<h4><?php printf(
    Yii::t('cati', 'Curso %d-%d'),
    $anyo_academico,
    $anyo_academico + 1
); ?></h4>

<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="menu-curso" data-toggle="dropdown">
        <?php echo Yii::t('cati', 'Cambiar de curso'); ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu-curso">
        <?php
        echo "<li role='presentation'>" . Html::a(
            Yii::t('cati', 'Anteriores'),
            "http://titulaciones.unizar.es/proy_titulaciones/programas/guias_global.php?titula={$estudio->id_nk}",
            ['target' => '_blank']
        ) . "</li>\n";

        for ($anyo = 2017; $anyo <= Calendario::getAnyoAcademico(); $anyo++) {
            echo '<li role="presentation">' . Html::a(
                $anyo . '/' . ($anyo + 1),
                [
                    'asignaturas',
                    'anyo_academico' => $anyo,
                    'estudio_id' => "{$anyo}" . str_pad("{$estudio->id_nk}", 4, "0", STR_PAD_LEFT),
                    'centro_id' => $centros[0]->id,
                    'plan_id_nk' => $plan->id_nk
                ],
                ['role' => 'menuitem']
            ) . "</li>\n";
        }
        ?>
    </ul>
</div>

<hr><br>


<div class="row breadcrumb">
    <p><?php
        printf(
            '<strong>%s</strong>: %d<br>',
            Yii::t('cati', 'Créditos'),
            $plan->creditos
        );
        printf(
            Yii::t('cati', '<strong>Duración</strong>: %d años académicos') . '<br>',
            $plan->duracion
        );
        printf(
            '<strong>%s</strong>: %s<br>',
            Yii::t('cati', 'Fecha BOE de plan de estudios'),
            Yii::$app->formatter->asDate($plan->fecha_boe)
        );
        printf(
            '<strong>%s</strong>: %s',
            Yii::t('cati', 'Regulación normativa'),
            Html::encode($plan->normativa)
        );
        ?><br>
    </p>

    <?php
    if ($es_curso_actual && $notas) {
        ?>
        <h3><?php echo Yii::t('cati', 'Notas del plan'); ?></h3>

        <p><?php echo HtmlPurifier::process($notas->texto); ?></p><br>
        <?php
    } ?>
</div>

<div class="row breadcrumb">
<ul class="listado">
<?php
// Este enlace tiene que salir sólo en los Grados y no en los Másteres
if (Estudio::GRADO_TIPO_ESTUDIO_ID == $estudio->tipoEstudio_id) {
    echo '<li>' . Html::a(
        Yii::t('cati', 'Actividades universitarias culturales y complementarias'),
        'https://academico.unizar.es/grado-y-master/reconocimiento-y-transferencia-de-creditos/reconocimiento-por-actividades',
        ['target' => '_blank']
    ) . ' <span class="glyphicon glyphicon-link"></span></li>';
}
?>
</ul>
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
        # El nombre de los itinerarios procede de la tabla `ODS_ITINERARIO_DESCRIPCION`
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

function nombreLargoPeriodo($periodo)
{
    $nombreLargo = str_replace('S', Yii::t('cati', 'Semestre '), $periodo);
    $nombreLargo = str_replace('C', Yii::t('cati', 'Cuatrimestre '), $nombreLargo);
    $nombreLargo = str_replace('A', Yii::t('cati', 'Anual'), $nombreLargo);

    return $nombreLargo;
}
$codigosPeriodos = array_filter(array_unique(array_column($asignaturas, 'periodo')));
$nombresPeriodos = array_map('nombreLargoPeriodo', $codigosPeriodos);
$periodos = array_combine($codigosPeriodos, $nombresPeriodos);

$cursos = array_filter(array_unique(array_column($asignaturas, 'curso')));
$cursos = array_combine($cursos, $cursos);

function traducirCaracter($clase)
{
    return Yii::t('clase', $clase);
}
$codigosCaracteres = array_filter(array_unique(array_column($asignaturas, 'clase')));
$nombresCaracteres = array_map('traducirCaracter', $codigosCaracteres);
$caracteres = array_combine($codigosCaracteres, $nombresCaracteres);

$idiomas = array_filter(
    array_unique(array_column($asignaturas, 'idioma')),
    function ($i) {
        return isset($i) and !strstr($i, '—');
    }
);
$idiomas = array_combine($idiomas, $idiomas);

$periodoFilter = Yii::$app->request->get('periodoFilter', '');
$cursoFilter = Yii::$app->request->get('cursoFilter', '');
$caracterFilter = Yii::$app->request->get('caracterFilter', '');
$idiomaFilter = Yii::$app->request->get('idiomaFilter', '');

$searchModel = new AsignaturaSearch();
$dataProvider = $searchModel->search(Yii::$app->request->queryParams);


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
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'curso',
                'label' => Yii::t('cati', 'Curso'),
                'filter' => Html::dropDownList(
                    'cursoFilter',
                    $cursoFilter,
                    $cursos,
                    ['prompt' => Yii::t('cati', 'Todos')]
                ),
                'format' => 'html',
            ],
            [
                'attribute' => 'periodo',
                'label' => Yii::t('cati', 'Periodo'),
                'value' => function ($asignatura) {
                    $tip = nombreLargoPeriodo(($asignatura['periodo']));

                    return "<div class='tooltip'>" . $asignatura['periodo'] .
                        "<span class='tooltiptext'>$tip</span></div>";
                },
                'filter' => Html::dropDownList(
                    'periodoFilter',
                    $periodoFilter,
                    $periodos,
                    ['prompt' => Yii::t('cati', 'Todos')]
                ),
                'format' => 'html',
            ],
            [
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
                            'centro_id' => $asignatura['centro_id'],
                            'plan_id_nk' => $plan->id_nk,
                        ]
                    );
                },
                'format' => 'html',
            ],
            [
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
                            'centro_id' => $asignatura['centro_id'],
                            'plan_id_nk' => $plan->id_nk,
                        ]
                    );
                },
                'format' => 'html',
            ],
            [
                'label' => Yii::t('cati', 'Carácter'),
                'value' => function ($asignatura) {
                    return Yii::t('clase', $asignatura['clase']);
                },
                'filter' => Html::dropDownList(
                    'caracterFilter',
                    $caracterFilter,
                    $caracteres,
                    ['prompt' => Yii::t('cati', 'Todos')]
                ),
                'format' => 'html',
            ],
            [
                'attribute' => 'creditos',
                'label' => Yii::t('cati', 'Créditos'),
                'format' => ['decimal', 1],
            ],
            [
                'attribute' => 'situacion',
                'label' => Yii::t('cati', ''),
                'value' => function ($asignatura) {
                    return ('Ofertada' === $asignatura['situacion']) ? ''
                        : str_replace(' ', '&nbsp;', $asignatura['situacion']);
                },
                'format' => 'html',
            ],
            [
                'label' => Yii::t('cati', 'Lím. plazas opt'),
                'value' => function ($asignatura) {
                    return $asignatura['numero_plazas'] ?
                        $asignatura['numero_plazas'] : '-';
                },
                'contentOptions' => ['style' => 'text-align: center;'],
                /*
                'visible' => function($asignatura) {
                    return $asignatura['hay_limite_plazas'];
                },*/
            ],
            [
                'attribute' => 'idioma',
                'label' => Yii::t('cati', 'Idioma de impartición'),
                'filter' => Html::dropDownList(
                    'idiomaFilter',
                    $idiomaFilter,
                    $idiomas,
                    ['prompt' => Yii::t('cati', 'Todos')]
                ),
                'format' => 'html',  // <span class="not-set">—</span> si no hay idioma
            ], /*[
                'attribute' => 'nombre_completo',
            ], */
        ],
        'options' => ['class' => 'cabecera-azul'],
        'summary' => '', // Do not show `Showing 1-19 of 19 items'.
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
    ]
);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end();
