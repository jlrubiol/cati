<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Calendario;

$this->title = Yii::t('gestion', 'Encuestas Doctorado') . ' ' . $anyo . '/' . ($anyo + 1);
$this->params['breadcrumbs'][] = $this->title;

$urlbase = Url::base() . '/pdf/encuestas/' . $anyo;
$pdfdir = Yii::getAlias('@webroot') . '/pdf/encuestas/' . $anyo;
$anyo_academico = Calendario::getAnyoDoctorado();
$anyos = range(2016, $anyo_academico - 1);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

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
                ['ver-encuestas-doct', 'anyo' => $anyo],
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
            'label' => Yii::t('cruds', 'Cód. plan'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Código del plan'),
            ],
        ], [
            'label' => Yii::t('gestion', 'Satisf. EST'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t(
                    'gestion',
                    'Satisfacción de los estudiantes con el doctorado'
                ),
            ],
            'value' => function ($plan) use ($urlbase, $pdfdir) {
                if (file_exists("{$pdfdir}/doctorado/{$plan->id_nk}/{$plan->id_nk}_InformeDoctoradoEst.pdf")) {
                    return Html::a(
                        Yii::t('gestion', 'Satisf. EST'),
                        "{$urlbase}/doctorado/{$plan->id_nk}/{$plan->id_nk}_InformeDoctoradoEst.pdf"
                    ) . "\n";
                }

                return '';
            },
            'contentOptions' => ['title' => Yii::t(
                'gestion',
                'Satisfacción de los estudiantes con el doctorado'
            )],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Satisf. PDI'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t(
                    'gestion',
                    'Satisfacción de los directores/tutores con el doctorado'
                ),
            ],
            'value' => function ($plan) use ($urlbase, $pdfdir) {
                if (file_exists("{$pdfdir}/doctorado/{$plan->id_nk}/{$plan->id_nk}_InformeDoctorado.pdf")) {
                    return Html::a(
                        Yii::t('gestion', 'Satisf. PDI'),
                        "{$urlbase}/doctorado/{$plan->id_nk}/{$plan->id_nk}_InformeDoctorado.pdf"
                    ) . "\n";
                }

                return '';
            },
            'contentOptions' => [
                'title' => Yii::t('gestion', 'Satisfacción de los directores/tutores con el doctorado')
            ],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Egresados'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t(
                    'gestion',
                    'Satisfacción e inserción laboral de egresados de la Escuela de Doctorado'
                ),
            ],
            'value' => function ($plan) use ($urlbase, $pdfdir) {
                if (file_exists("{$pdfdir}/doctoradoEgresados/{$plan->id_nk}/{$plan->id_nk}_InformeDoctoradoEgresados.pdf")) {
                    return Html::a(
                        Yii::t('gestion', 'Egresados'),
                        "{$urlbase}/doctoradoEgresados/{$plan->id_nk}/{$plan->id_nk}_InformeDoctoradoEgresados.pdf"
                    ) . "\n";
                }

                return '';
            },
            'contentOptions' => ['title' => Yii::t(
                'gestion',
                'Satisfacción e inserción laboral de egresados de la Escuela de Doctorado'
            )],
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
