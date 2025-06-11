<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $asignatura['descripcion'];
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $anyo_academico],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('cati', 'Asignaturas del plan ') . $plan->id_nk,
    'url' => [
        'estudio/asignaturas',
        'anyo_academico' => $anyo_academico,
        'estudio_id' => $estudio->id,
        'plan_id_nk' => $plan->id_nk,
        'centro_id' => $centro->id,
    ],
];
$this->params['breadcrumbs'][] = $this->title;

$asignatura['periodo'] = str_replace('S', Yii::t('cati', 'Semestre '), $asignatura['periodo']);
$asignatura['periodo'] = str_replace('C', Yii::t('cati', 'Cuatrimestre '), $asignatura['periodo']);
$asignatura['periodo'] = str_replace('A', Yii::t('cati', 'Anual'), $asignatura['periodo']);
$language = Yii::$app->language;

$guia = "{$asignatura['asignatura_id']}_$language.pdf";

$profes = [];
foreach ($profesores as $profesor) {
    $profes[] = Html::a(
        Html::encode($profesor['nombre_completo']),
        sprintf('https://janovas.unizar.es/sideral/CV/%s?lang=%s', trim($profesor['URL']), Yii::$app->language)
    ) . ' <span class="glyphicon glyphicon-link"></span>';
}
$profesores_str = join(', ', $profes);
$asignatura['profesores'] = $profesores_str;

?>

<h1><?php echo Html::encode($estudio->nombre); ?></h1>
<h2><?php echo Html::encode($centro->nombre); ?></h2>
<h3><?php echo Html::encode($asignatura['descripcion']); ?></h3>
<h4><?php printf(Yii::t('cati', 'Curso %d-%d'), $anyo_academico, $anyo_academico + 1); ?></h4>

<hr><br>

<div class="container">

<?php
echo DetailView::widget([
    'model' => $asignatura,
    'attributes' => [
        [
            'attribute' => 'curso',
            'label' => Yii::t('cati', 'Curso'),
        ],
        [
            'attribute' => 'asignatura_id',
            'label' => Yii::t('cati', 'Código'),
        ], [
            'attribute' => 'descripcion',
            'label' => Yii::t('cati', 'Nombre'),
        ], [
            'label' => Yii::t('cati', 'Carácter'),
            'value' => Yii::t('clase', $asignatura['clase']),
        ], /* [
            'label' => Yii::t('cati', 'Tipo'),
            'attribute' => 'tipo',
        ], */ [
            'attribute' => 'creditos',
            'label' => Yii::t('cati', 'Créditos'),
            'format' => ['decimal', 1],
        ], [
            'attribute' => 'periodo',
            'label' => Yii::t('cati', 'Periodo'),
        ], [
            'attribute' => 'situacion',
            'label' => Yii::t('cati', 'Situación'),
            'value' => Yii::t('situacion', $asignatura['situacion']),
        ], [
            'label' => Yii::t('cati', 'Lím. plazas opt'),
            'attribute' => 'numero_plazas',
            'visible' => $asignatura['hay_limite_plazas'],
        ], [
            'attribute' => 'idioma',
            'label' => Yii::t('cati', 'Idioma de impartición'),
            'format' => 'html',
        ], [
            'attribute' => 'profesores',
            'label' => Yii::t('cati', 'Profesores'),
            'format' => 'html',
        ], [
            'label' => Yii::t('cati', 'Guía docente'),
            'value' => function ($asignatura) use ($language, $anyo_academico, $urlGuias, $guia) {
                $idiomaPais = ('en' == $language) ? 'en.GB' : 'es.ES';

                $respuesta = Html::a(
                    Yii::t('cati', 'Formato web'),
                    "{$urlGuias}/doa/consultaPublica/look[conpub]MostrarPubGuiaDocAs?entradaPublica=true&idiomaPais=$idiomaPais&_anoAcademico=$anyo_academico&_codAsignatura={$asignatura['asignatura_id']}",
                    ['target' => '_blank']
                ) . ' <span class="glyphicon glyphicon-link"></span>' . ' / ' .
                Html::a(
                    Yii::t('cati', 'Formato PDF'),
                    "{$urlGuias}/documentos/doa/guiadocente/{$anyo_academico}/{$guia}",
                    ['target' => '_blank']
                ) . ' <span class="glyphicon glyphicon-link"></span>';

                if ($anyo_academico == 2019) {
                    $respuesta .= ' / ' . Html::a(
                        Yii::t('cati', 'Adendas curso 2019-20'),
                        'https://academico.unizar.es/grado-y-master/adendas-guias-docentes/adendas-guias-docentes-curso-2019-20',
                        ['target' => '_blank']
                    ) . ' <span class="glyphicon glyphicon-link"></span>';
                }

                return $respuesta;
            },
            'format' => 'raw',  // 'html' lo pasaría por HtmlPurifier, que eliminaría el target
            'visible' => $tiene_ficha,
        ], [
            'label' => Yii::t('cati', 'Bibliografía recomendada'),
            'value' => function ($asignatura) {
                return Html::a(
                    Yii::t('cati', 'Bibliografía'),
                    "http://psfunizar10.unizar.es/br13/egAsignaturas.php?codigo={$asignatura['asignatura_id']}",
                    ['target' => '_blank']
                ) . ' <span class="glyphicon glyphicon-link"></span>';
            },
            'format' => 'raw',  // 'html' lo pasaría por HtmlPurifier, que eliminaría el target
        ],
    ],
]);
?>
</div> <!-- container -->
