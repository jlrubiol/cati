<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Calendario;

$this->title = Yii::t('gestion', 'Encuestas Grado y Máster') . ' ' . $anyo . '/' . ($anyo + 1);
$this->params['breadcrumbs'][] = $this->title;

$urlbase = Url::base() . '/pdf/encuestas/' . $anyo;
$pdfdir = Yii::getAlias('@webroot') . '/pdf/encuestas/' . $anyo;
$anyo_academico = Calendario::getAnyoAcademico();
$anyos = range($anyo_academico - 7, $anyo_academico - 1);

# https://gitlab.intra.unizar.es/327618/planificacion-web-estudios/-/issues/21
$nombre_ensenanza = Yii::t('cati', 'Docencia');
$titulo_ensenanza = Yii::t('cati', 'Valoración de la docencia (bloque enseñanza)');
if ($anyo < 2022) {
    $nombre_ensenanza = Yii::t('cati', 'Enseñanza');
    $titulo_ensenanza = Yii::t('cati', 'Evaluación de la enseñanza');
}
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<!--
<form name="jump">
    <?php /* echo Yii::t('gestion', 'Curso') */ ?>:
    <select name="anyo" onchange="window.location.assign(this.options[this.selectedIndex].value);">
        <option value="#" selected="selected">Seleccione</option>
        <?php /*
        foreach ($anyos as $anyo) {
            $url = Url::to(['ver-encuestas', 'anyo' => $anyo]);
            echo "<option value='$url'>".$anyo.'/'.($anyo + 1)."</option>\n";
        }
        */ ?>
    </select>
</form><br>
-->

<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="menu-curso"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <?php echo Yii::t('gestion', 'Curso'); ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu-curso">
        <?php
        foreach ($anyos as $anyo) {
            echo '<li role="presentation">' . Html::a(
                $anyo . '/' . ($anyo + 1),
                ['ver-encuestas', 'anyo' => $anyo],
                ['role' => 'menuitem']
            ) . "</li>\n";
        }
        ?>
    </ul>
</div><br>

<?php
$dataProvider = new ArrayDataProvider([
    'allModels' => $planes,
    // 'pagination' => false,  // ['pageSize' => 10],
    'sort' => [
        'attributes' => ['estudio.id_nk', 'estudio.nombre', 'id_nk'],
        'defaultOrder' => [
            'estudio.nombre' => SORT_ASC,
            'id_nk' => SORT_ASC,
        ],
    ],
]);

\yii\widgets\Pjax::begin(
    [
        'id' => 'pjax-main',
        'enableReplaceState' => false,
        'linkSelector' => '#pjax-main ul.pagination a, th a',
        // 'clientOptions' => ['pjax:success' => 'function() { alert("yo"); }'],
    ]
);

echo "<div class='table-responsive'>";
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'estudio.id_nk',
            'label' => Yii::t('cruds', 'Cód. estudio'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Código del estudio'),
            ],
        ], [
            'attribute' => 'estudio.nombre',
            'label' => Yii::t('cruds', 'Estudio'),
            'value' => function ($plan) {
                return Html::a(
                    $plan->estudio->nombre,
                    ['estudio/ver', 'id' => $plan->estudio->id_nk]
                );
            },
            'format' => 'html',
        ], [
            'attribute' => 'id_nk',
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Código del plan'),
            ],
        ], [
            'label' => $nombre_ensenanza,
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => $titulo_ensenanza,
            ],
            'value' => function ($plan) use ($urlbase, $pdfdir, $nombre_ensenanza) {
                if (file_exists("{$pdfdir}/ensenanza/{$plan->centro->id}/{$plan->id_nk}_InformeEnsenanzaTitulacion.pdf")) {
                    return Html::a(
                        $nombre_ensenanza,
                        "{$urlbase}/ensenanza/{$plan->centro->id}/{$plan->id_nk}_InformeEnsenanzaTitulacion.pdf"
                    ) . "\n";
                }

                return '';
            },
            'contentOptions' => ['title' => $titulo_ensenanza],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Movilidad'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Programas de movilidad: Erasmus'),
            ],
            'value' => function ($plan) use ($urlbase, $pdfdir) {
                if (file_exists("{$pdfdir}/movilidad/{$plan->centro->id}/{$plan->id_nk}_InformeMovilidad.pdf")) {
                    return Html::a(
                        Yii::t('gestion', 'Movilidad'),
                        "{$urlbase}/movilidad/{$plan->centro->id}/{$plan->id_nk}_InformeMovilidad.pdf"
                    ) . "\n";
                }

                return '';
            },
            'contentOptions' => ['title' => Yii::t('gestion', 'Programas de movilidad: Erasmus')],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Prácticas'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Evaluación de las prácticas externas por los alumnos'),
            ],
            'value' => function ($plan) use ($urlbase, $pdfdir) {
                if (file_exists("{$pdfdir}/practicas/{$plan->centro->id}/{$plan->id_nk}_InformePracticasTitulacion.pdf")) {
                    return Html::a(
                        Yii::t('gestion', 'Prácticas'),
                        "{$urlbase}/practicas/{$plan->centro->id}/{$plan->id_nk}_InformePracticasTitulacion.pdf"
                    ) . "\n";
                }

                return '';
            },
            'contentOptions' => ['title' => Yii::t('gestion', 'Evaluación de las prácticas externas por los alumnos')],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Satisf. PAS'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Satisfacción del PAS con el centro'),
            ],
            'value' => function ($plan) use ($urlbase, $pdfdir) {
                // Por centro, no por plan
                if (file_exists("{$pdfdir}/satisfaccionPAS/{$plan->centro->id}/{$plan->centro->id}_InformeSatisfaccionPAS.pdf")) {
                    return Html::a(
                        Yii::t('gestion', 'Satisf. PAS'),
                        "{$urlbase}/satisfaccionPAS/{$plan->centro->id}/{$plan->centro->id}_InformeSatisfaccionPAS.pdf"
                    ) . "\n";
                }

                return '';
            },
            'contentOptions' => ['title' => Yii::t('gestion', 'Satisfacción del PAS con el centro')],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Satisf. PDI'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Satisfacción del PDI con la titulación'),
            ],
            'value' => function ($plan) use ($urlbase, $pdfdir) {
                if (file_exists("{$pdfdir}/satisfaccionPDI/{$plan->centro->id}/{$plan->id_nk}_InformeSatisfaccionPDI.pdf")) {
                    return Html::a(
                        Yii::t('gestion', 'Satisf. PDI'),
                        "{$urlbase}/satisfaccionPDI/{$plan->centro->id}/{$plan->id_nk}_InformeSatisfaccionPDI.pdf"
                    ) . "\n";
                } else {
                    return '';
                }
            },
            'contentOptions' => ['title' => Yii::t('gestion', 'Satisfacción del PDI con la titulación')],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Satisf. EST'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Satisfacción de los estudiantes con la titulación'),
            ],
            'value' => function ($plan) use ($urlbase, $pdfdir) {
                if (file_exists("{$pdfdir}/satisfaccionTitulacion/{$plan->centro->id}/{$plan->id_nk}_InformeSatisfaccionTitulacionEstudiantes.pdf")) {
                    return Html::a(
                        Yii::t('gestion', 'Satisf. EST'),
                        "{$urlbase}/satisfaccionTitulacion/{$plan->centro->id}/{$plan->id_nk}_InformeSatisfaccionTitulacionEstudiantes.pdf"
                    ) . "\n";
                } else {
                    return '';
                }
            },
            'contentOptions' => ['title' => Yii::t('gestion', 'Satisfacción de los estudiantes con la titulación')],
            'format' => 'html',
        ],
    ],
    'options' => ['class' => 'cabecera-azul'],
    // 'summary' => '', // Do not show `Showing 1-19 of 19 items'.
    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
]);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end();
