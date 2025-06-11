<?php
/**
 * Lista de los indicadores de calidad de los PD de 2013/2014 a 2016-2017.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Indicadores de calidad');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/doctorado']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Yii::t('gestion', 'Indicadores de calidad de los programas'); ?></h1>
<hr><br>

<div class="table-responsive">
    <?php
    echo GridView::widget([
        'dataProvider' => $dpPlanes,
        'columns' => [
            [
                'attribute' => 'estudio_id_nk',
                'headerOptions' => [  // HTML attributes for the header cell tag
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'title' => Yii::t('gestion', 'Código del estudio'),
                ],
            ], [
                'attribute' => 'id_nk',
                'headerOptions' => [  // HTML attributes for the header cell tag
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'title' => Yii::t('gestion', 'Código del plan'),
                ],
            ], [
                'attribute' => 'estudio.nombre',
                'label' => Yii::t('cruds', 'Estudio'),
                'value' => function ($plan) {
                    return Html::a(
                        $plan->estudio->nombre,
                        ['estudio/ver-doct', 'id' => $plan->estudio_id_nk]
                    );
                },
                'format' => 'html',
            ], [
                // 'label' => Yii::t('cruds', 'Subir indicadores'),
                'value' => function ($plan) {
                    return Html::a(
                        'Subir indicadores',
                        ['subir-indicadores', 'estudio_id_nk' => $plan->estudio_id_nk]
                    );
                },
                'format' => 'html',
            ], [
                'value' => function ($plan) {
                    if (file_exists("pdf/indicadores/indicadores-{$plan->estudio_id_nk}.pdf")) {
                        return Html::a(
                            'Ver indicadores',
                            Url::home() . "pdf/indicadores/indicadores-{$plan->estudio_id_nk}.pdf"
                        );
                    }

                    return '';
                },
                'format' => 'html',
            ],
        ],
        'options' => ['class' => 'cabecera-azul'],
        // 'summary' => false,  // Do not show `Showing 1-19 of 19 items'.
    ]);
    ?>
</div>
