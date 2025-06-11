<?php

use app\models\InformePublicado;
use app\models\PlanPublicado;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('cati', "Informes y planes de doctorado elaborados en el curso {$anyo}/{$siguiente_anyo} (campaña {$siguiente_anyo})");
$this->params['breadcrumbs'][] = $this->title;

$urlinformes = Url::base() . '/pdf/informes/' . $anyo;
$urlplanes = Url::base() . '/pdf/planes-mejora/' . $anyo;

$anyos = range(2016, $ultimo_anyo);
$language = Yii::$app->language;
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="menu-curso" data-toggle="dropdown">
        <?php echo Yii::t('cati', 'Curso de elaboración'); ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu-curso">
        <?php
        foreach ($anyos as $anyo) {
            echo '<li role="presentation">';
            echo Html::a(
                $anyo . '/' . ($anyo + 1),
                ['acpua-doct', 'anyo' => $anyo],
                ['role' => 'menuitem']
            );
            echo "</li>\n";
        }
        ?>
    </ul>
</div><br>

<?php
$dataProvider = new ArrayDataProvider([
    'allModels' => $estudios,
    'pagination' => false,  // ['pageSize' => 10],
    'sort' => [
        'attributes' => ['nombre'],
        'defaultOrder' => [
            'nombre' => SORT_ASC,
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
            'attribute' => 'id_nk',
            'label' => Yii::t('gestion', 'Cód. estudio'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Código del estudio'),
            ],
        ], [
            'label' => Yii::t('cruds', 'Estudio'),
            'value' => function ($estudio) {
                return Html::a(
                    Html::encode($estudio->nombre),
                    ['estudio/ver-doct', 'id' => $estudio->id_nk]
                );
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('cati', 'Informe de la calidad'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('cati', 'Informe de Evaluación de la Calidad'),
            ],
            'value' => function ($estudio) use ($urlinformes, $language, $informes_publicados) {
                if (isset($informes_publicados[$estudio->id])
                    and InformePublicado::MAX_VERSION_INFORME_DOCT <= $informes_publicados[$estudio->id]->version
                ) {
                    return Html::a(
                        Yii::t('cati', 'Informe'),
                        sprintf('%s/informe-%s-%d-v%d.pdf', $urlinformes, $language, $estudio->id_nk, InformePublicado::MAX_VERSION_INFORME_DOCT)
                    );
                }
                return '';
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('cati', 'Plan de mejora'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('cati', 'Plan anual de innovación y mejora'),
            ],
            'value' => function ($estudio) use ($urlplanes, $language, $planes_publicados) {
                if (isset($planes_publicados[$estudio->id])) {
                    $max_version_plan_doct = $planes_publicados[$estudio->id]->getVersionMaxima();
                    if ($max_version_plan_doct <= $planes_publicados[$estudio->id]->version) {
                        return Html::a(
                            Yii::t('cati', 'Plan'),
                            sprintf('%s/plan-%s-%d-v%d.pdf', $urlplanes, $language, $estudio->id_nk, $max_version_plan_doct)
                        );
                    }
                }
                return '';
            },
            'format' => 'html',
        ],
    ],
    'options' => ['class' => 'cabecera-azul'],
    // 'summary' => '',  // Do not show `Showing 1-19 of 19 items'.
    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
]);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end();
