<?php
/**
 * Vista de la lista de los informes de evaluación en una versión dada.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use app\models\InformePublicado;
use kartik\icons\Icon;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

Icon::map($this, Icon::FA); // Maps the Font Awesome icon font framework

switch ($tipo) {
    case 'grado-master':
        $nombre_lista = Yii::t('gestion', 'Informes de Grado y Máster');
        $max_version = InformePublicado::MAX_VERSION_INFORME;
        break;
    case 'doctorado':
        $nombre_lista = Yii::t('gestion', 'Informes de Doctorado');
        $max_version = InformePublicado::MAX_VERSION_INFORME_DOCT;
        break;
    default:
        throw new NotFoundHttpException(sprintf(
            Yii::t('cati', 'No se ha encontrado el tipo de estudio «%s».  ☹'),
            $tipo
        ));
}
$this->title = Yii::t('gestion', 'Informes') . ' ' . $anyo . '/' . ($anyo + 1) . ' – ' . Yii::t('gestion', 'Versión') . ' ' . $version;
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => $nombre_lista . ' ' . $anyo . '/' . ($anyo + 1),
    'url' => ['gestion/lista-informes', 'anyo' => $anyo, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);

$fv = Yii::$app->request->getQueryParam('version', false);
$buttontext = false !== $fv ? Yii::t('gestion', 'Versión') . ' ' . Html::encode($fv)
                            : Yii::t('gestion', 'Todas las versiones');
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="menu-version"
       data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <?php echo $buttontext; ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu-version">
        <?php
        echo '<li role="presentation">' . Html::a(
            Yii::t('gestion', 'Todas las versiones'),
            ['lista-informes', 'anyo' => $anyo, 'tipo' => $tipo],
            ['role' => 'menuitem']
        ) . "</li>\n";
        for ($v = 0; $v <= $max_version; ++$v) {
            echo '<li role="presentation">' . Html::a(
                Yii::t('gestion', 'Versión') . ' ' . $v,
                ['lista-informes-version', 'anyo' => $anyo, 'tipo' => $tipo, 'version' => $v],
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
            'label' => Yii::t('gestion', 'Ver'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Ver el informe'),
                'style' => 'text-align: center;',
            ],
            'value' => function ($estudio) use ($anyo, &$contestadas) {
                if (isset($contestadas[$estudio->id]) and $contestadas[$estudio->id] > 0) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-eye-open"></span> ' .
                        Yii::t('gestion', 'Ver'),
                        ['informe/ver', 'estudio_id' => $estudio->id, 'anyo' => $anyo],
                        ['id' => 'ver-informe', 'class' => 'btn btn-success btn-xs', 'title' => Yii::t('gestion', 'Ver el informe')] // Button
                    );
                }

                return '';
            },
            'contentOptions' => ['style' => 'text-align: center;'],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Editar'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Editar el informe'),
                'style' => 'text-align: center;',
            ],
            'value' => function ($estudio) use ($anyo, $informes_publicados) {
                $informe = isset($informes_publicados[$estudio->id]) ? $informes_publicados[$estudio->id] : null;
                if (!$informe or $informe->esEditable()) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-pencil"></span> ' .
                        Yii::t('gestion', 'Editar'),
                        ['informe/editar', 'estudio_id' => $estudio->id, 'anyo' => $anyo],
                        ['id' => 'editar-informe', 'class' => 'btn btn-info btn-xs', 'title' => Yii::t('gestion', 'Editar el informe')]  // Button
                    );
                }
                return '';
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
            'value' => function ($estudio) use (&$informes_publicados) {
                $informe = isset($informes_publicados[$estudio->id]) ? $informes_publicados[$estudio->id] : null;

                return $informe ? $informe['version'] : 0;
            },
            'contentOptions' => function ($model, $key, $index, $column) use (&$informes_publicados) {
                // $colors = ['#cc3333', '#ffff66', '#33cc66']; // red, yellow, green
                $colors = ['#d9534f', '#f0ad4e', '#5cb85c']; // rojo, amarillo, verde
                $informe = isset($informes_publicados[$model->id]) ? $informes_publicados[$model->id] : null;
                $version = $informe ? $informe['version'] : 0;

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
            'value' => function ($estudio) use ($anyo, &$informes_publicados) {
                $informe = isset($informes_publicados[$estudio->id]) ? $informes_publicados[$estudio->id] : null;
                if ($informe and $informe->version > 0) {
                    return Html::a(
                        // '<span class="glyphicon glyphicon-unlock"></span> '.
                        // Unlock glyphicon is not included in Bootstrap.  Let's use Font Awesome!
                        Icon::show('unlock-alt', ['class' => 'fa-lg'], Icon::FA) .
                        Yii::t('gestion', 'Abrir'),
                        ['gestion/abrir-informe', 'estudio_id' => $estudio->id, 'anyo' => $anyo],
                        ['id' => 'abrir-informe', 'class' => 'btn btn-success btn-xs', 'title' => Yii::t('gestion', 'Volver a abrir la última versión publicada')] // Button
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
            'value' => function ($estudio) use ($anyo, &$informes_publicados) {
                $informe = isset($informes_publicados[$estudio->id]) ? $informes_publicados[$estudio->id] : null;
                if (!$informe or $informe->version < InformePublicado::MAX_VERSION_INFORME) {
                    return Html::a(
                        // '<span class="glyphicon glyphicon-lock"></span> '.
                        Icon::show('lock', ['class' => 'fa-lg'], Icon::FA) .
                        Yii::t('gestion', 'Cerrar'),
                        ['gestion/cerrar-informe', 'estudio_id' => $estudio->id, 'anyo' => $anyo],
                        ['id' => 'cerrar-informe', 'class' => 'btn btn-danger btn-xs', 'title' => Yii::t('gestion', 'Cerrar la versión actualmente en edición')] // Button
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
            // 'format' => 'html',
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
