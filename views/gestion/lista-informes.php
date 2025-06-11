<?php

use app\models\InformePublicado;
use app\models\Plan;
use kartik\icons\Icon;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

Icon::map($this, Icon::FA); // Maps the Font Awesome icon font framework

$this->title = Yii::t('gestion', 'Informes de Grado y Máster') . ' ' . $anyo . '/' . ($anyo + 1);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<div class="alert alert-info alert-dismissable fade in">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <p>La versión inicial de un informe es «0».  Desde la página para ver el borrador del
     informe, el coordinador puede cerrar la versión.  El número de versión se incrementa
     y se envía el documento por correo electrónico.  El proceso se repite hasta alcanzar
     la versión final («2»).</p>

  <p>En esta página la Unidad de Calidad puede incrementar o disminuir sucesivamente
     el número de versión, bien sea para impedir que los coordinadores editen el informe
     de una titulación, o bien para permitirles volver a generar una versión ya cerrada.
     En cualquier caso, al modificar el número de versión en esta página <b>no se generan
     PDF</b> ni se envían correos.</p>

  <p>Para que genere el PDF y se envíen los correos, hay que cerrar cada informe desde su propia página.</p>
</div>

<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="menu-version"
      data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <?php echo Yii::t('gestion', 'Versión'); ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu-version">
        <?php
        for ($v = 0; $v <= InformePublicado::MAX_VERSION_INFORME; ++$v) {
            echo '<li role="presentation">' . Html::a(
                Yii::t('gestion', 'Versión') . ' ' . $v,
                ['lista-informes-version', 'anyo' => $anyo, 'tipo' => 'grado-master', 'version' => $v],
                ['role' => 'menuitem']
            ) . "</li>\n";
        }
        ?>
    </ul>
</div><br>

<?php
$dataProvider = new ArrayDataProvider([
    'allModels' => $estudios,
    'pagination' => false, // ['pageSize' => 10],
    'sort' => [
        'attributes' => ['nombre', 'id_nk'],
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
            'attribute' => 'nombre',
            'label' => Yii::t('cruds', 'Estudio'),
            'value' => function ($estudio) use ($anyo) {
                return Html::a(
                    $estudio->nombre,
                    ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $anyo]
                );
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Ver/Editar'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Ver y/o editar el informe'),
                'style' => 'text-align: center;',
            ],
            'value' => function ($estudio) {
                return Html::a(
                    '<span class="glyphicon glyphicon-pencil"></span> ' .  Yii::t('gestion', 'Ver/Editar'),
                    ['informe/ver', 'estudio_id' => $estudio->id, 'anyo' => $estudio->anyo_academico],
                    [
                        'id' => 'ver-informe',
                        'class' => 'btn btn-info btn-xs',
                        'title' => Yii::t('gestion', 'Ver y/o editar el informe'),
                    ]
                );
            },
            'contentOptions' => ['style' => 'text-align: center;'],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Versión'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Última versión publicada del informe'),
                'style' => 'text-align: center;',
            ],
            'value' => function ($estudio) use ($anyo) {
                return $estudio->getVersionInforme($anyo);
            },
            'contentOptions' => function ($model, $key, $index, $column) use ($anyo) {
                // $colors = ['#cc3333', '#ffff66', '#33cc66']; // red, yellow, green
                $colors = ['#d9534f', '#f0ad4e', '#5cb85c']; // rojo, amarillo, verde
                $version = $model->getVersionInforme($anyo);

                return [
                    'style' => 'text-align: center; background-color: ' . $colors[$version] . ';',
                    'title' => Yii::t('gestion', 'Última versión publicada del informe'),
                ];
            },
            // 'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Abrir'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Volver a abrir la última versión publicada'),
                'style' => 'text-align: center;',
            ],
            'value' => function ($estudio) use ($anyo) {
                if ($estudio->getVersionInforme($anyo) > 0) {
                    return Html::a(
                        // '<span class="glyphicon glyphicon-unlock"></span> '.
                        // Unlock glyphicon is not included in Bootstrap.  Let's use Font Awesome!
                        Icon::show('unlock-alt', ['class' => 'fa-lg'], Icon::FA) .
                        Yii::t('gestion', 'Abrir'),
                        ['gestion/abrir-informe', 'estudio_id' => $estudio->id, 'anyo' => $anyo],
                        [
                            'id' => 'abrir-informe',
                            'class' => 'btn btn-success btn-xs',
                            'title' => Yii::t('gestion', 'Volver a abrir la última versión publicada'),
                        ]  // Button
                    );
                }

                return '';
            },
            'contentOptions' => ['style' => 'text-align: center;'],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Cerrar'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Cerrar la versión actualmente en edición'),
                'style' => 'text-align: center;',
            ],
            'value' => function ($estudio) use ($anyo) {
                if ($estudio->getVersionInforme($anyo) < InformePublicado::MAX_VERSION_INFORME) {
                    return Html::a(
                        // '<span class="glyphicon glyphicon-lock"></span> '.
                        Icon::show('lock', ['class' => 'fa-lg'], Icon::FA) .
                        Yii::t('gestion', 'Cerrar'),
                        ['gestion/cerrar-informe', 'estudio_id' => $estudio->id, 'anyo' => $anyo],
                        [
                            'id' => 'cerrar-informe',
                            'class' => 'btn btn-danger btn-xs',
                            'title' => Yii::t('gestion', 'Cerrar la versión actualmente en edición'),
                        ]  // Button
                    );
                }

                return '';
            },
            'contentOptions' => ['style' => 'text-align: center;'],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Contestadas'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Cantidad de preguntas contestadas'),
                'style' => 'text-align: center;',
            ],
            'value' => function ($estudio) use (&$contestadas) {
                $num = isset($contestadas[$estudio->id]) ? $contestadas[$estudio->id] : 0;

                return $num;
            },
            'contentOptions' => [
                'style' => 'text-align: center;',
                'title' => Yii::t('gestion', 'Cantidad de preguntas contestadas'),
            ],
            // 'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Coordinadores'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Coordinadores del estudio'),
            ],
            'value' => function ($estudio) {
                $planes = $estudio->plans;
                $correos = join(', ', array_unique(array_filter(array_map(function ($plan) {
                    return $plan->email_coordinador;
                }, $planes))));

                return $correos;
            },
            'format' => 'html',
            'format' => 'email', // See http://www.yiiframework.com/doc-2.0/guide-output-formatting.html
        ],
    ],
    'options' => ['class' => 'cabecera-azul'],
    'summary' => '', // Do not show `Showing 1-19 of 19 items'.
    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
]);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end();
