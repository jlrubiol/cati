<?php
/**
 * Vista de la lista de los planes de innovación y mejora.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use app\models\PlanPublicado;
use kartik\icons\Icon;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

Icon::map($this, Icon::FA); // Maps the Font Awesome icon font framework

$siguiente_anyo = $anyo + 1;
$siguesigue_anyo = $siguiente_anyo + 1;

$this->title = Yii::t('gestion', "Planes de innovación y mejora para el curso {$siguiente_anyo}/{$siguesigue_anyo} (campaña {$siguiente_anyo})");
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);

$fv = Yii::$app->request->getQueryParam('filterversion', false);
$buttontext = false !== $fv ? Yii::t('gestion', 'Versión') . ' ' . Html::encode($fv)
                            : Yii::t('gestion', 'Todas las versiones');

$version_maxima = ($tipo == 'grado-master') ? PlanPublicado::MAX_VERSION_PLAN : PlanPublicado::MAX_VERSION_PLAN_DOCT;
$version_maxima = PlanPublicado::MAX_VERSION_PLAN;  // Grado-Master
if ($tipo == 'doctorado') {
    $version_maxima = PlanPublicado::MAX_VERSION_PLAN_DOCT;
    # En el curso 2021-22, PlanPublicado::MAX_VERSION_PLAN_DOCT pasó de ser 1 a 2.
    if ($anyo < 2021) {
        $version_maxima -= 1;
    }
}
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<div class="alert alert-info alert-dismissable fade in">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <p>La versión inicial de un plan de innovación y mejora es «0».  Desde la página
     para ver el borrador del plan, el coordinador puede cerrar la versión.  El
     número de versión se incrementa y se envía el documento por correo electrónico.
     El proceso se repite hasta alcanzar la versión final («<?php echo $version_maxima; ?>»).</p>

  <p>En esta página la Unidad de Calidad puede incrementar o disminuir sucesivamente
     el número de versión, bien sea para impedir que los coordinadores editen el plan
     de mejora de una titulación, o bien para permitirles volver a generar una
     versión ya cerrada.  En cualquier caso, al modificar el número de versión en
     esta página <b>no se generan PDF</b> ni se envían correos.</p>

  <p>Para que genere el PDF y se envíen los correos, hay que cerrar cada PAIM desde su propia página.</p>
</div>

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
            ['lista-planes', 'anyo' => $anyo, 'tipo' => $tipo],
            ['role' => 'menuitem']
        ) . "</li>\n";
        for ($v = 0; $v <= $version_maxima; ++$v) {
            echo '<li role="presentation">' . Html::a(
                Yii::t('gestion', 'Versión') . ' ' . $v,
                ['lista-planes', 'anyo' => $anyo, 'tipo' => $tipo, 'filterversion' => $v],
                ['role' => 'menuitem']
            ) . "</li>\n";
        }
        ?>
    </ul>
</div><br>


<?php
$searchAttributes = ['version'];
$searchModel = [];

foreach ($searchAttributes as $searchAttribute) {
    $filterName = 'filter' . $searchAttribute;
    $filterValue = Yii::$app->request->getQueryParam($filterName, '');
    if ($filterValue != '') { $filterValue = intval($filterValue);}
    $searchModel[$searchAttribute] = $filterValue;
    $datos = array_filter($datos, function ($item) use (&$filterValue, &$searchAttribute) {
        // return strlen($filterValue) > 0 ? stripos('/^' . strtolower($item[$searchAttribute]) . '/', strtolower($filterValue)) : true;
        return strlen($filterValue) > 0 ? $filterValue === $item[$searchAttribute] : true;
    });
}

$dataProvider = new ArrayDataProvider([
    'allModels' => $datos,
    'pagination' => false,  // ['pageSize' => 10],
    'sort' => [
        'attributes' => ['id_nk', 'nombre', 'version', 'celdas'],
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
    // 'filterModel' => $searchModel,
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
            'value' => function ($dato) use ($anyo) {
                return Html::a(
                    $dato['nombre'],
                    ['estudio/ver', 'id' => $dato['id_nk'], 'anyo_academico' => $anyo]
                );
            },
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Ver/Editar'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Ver o editar el plan'),
                'style' => 'text-align: center;',
            ],
            'value' => function ($dato) use ($anyo) {
                return Html::a(
                    '<span class="glyphicon glyphicon-pencil"></span> ' .
                    Yii::t('gestion', 'Ver/Editar'),
                    ['plan-mejora/ver', 'estudio_id' => $dato['id'], 'anyo' => $anyo],
                    ['id' => 'ver-plan', 'class' => 'btn btn-info btn-xs', 'title' => Yii::t('gestion', 'Ver o editar el plan')]  // Button
                );
            },
            'contentOptions' => ['style' => 'text-align: center;'],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Versión'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Última versión publicada del plan'),
                'style' => 'text-align: center;',
            ],
            'attribute' => 'version',
            // 'filter' => '<input class="form-control" name="filterversion" value="' . $searchModel['version'] . '" type="text">',
            'contentOptions' => function ($model, $key, $index, $column) {
                $colors = ['#d9534f', '#ff8c00', '#f0ad4e', '#5cb85c']; // rojo, naranja, amarillo, verde
                $version = $model['version'];

                return [
                    'style' => 'text-align: center; background-color: ' . $colors[$version] . ';',
                    'title' => Yii::t('gestion', 'Última versión publicada del plan'),
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
            'value' => function ($dato) use ($anyo) {
                if ($dato['version'] > 0) {
                    return Html::a(
                        // '<span class="glyphicon glyphicon-unlock"></span> '.
                        // Not included in Bootstrap.  Let's use Font Awesome
                        Icon::show('unlock-alt', ['class' => 'fa-lg'], Icon::FA) .
                        Yii::t('gestion', 'Abrir'),
                        ['gestion/abrir-plan', 'estudio_id' => $dato['id'], 'anyo' => $anyo],
                        ['id' => 'abrir-plan', 'class' => 'btn btn-success btn-xs', 'title' => Yii::t('gestion', 'Volver a abrir la última versión publicada')] // Button
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
            'value' => function ($dato) use ($anyo) {
                if ($dato['version'] < $dato['version_maxima']) {
                    return Html::a(
                        // '<span class="glyphicon glyphicon-lock"></span> '.
                        Icon::show('lock', ['class' => 'fa-lg'], Icon::FA) .
                        Yii::t('gestion', 'Cerrar'),
                        ['gestion/cerrar-plan', 'estudio_id' => $dato['id'], 'anyo' => $anyo],
                        ['id' => 'cerrar-plan', 'class' => 'btn btn-danger btn-xs', 'title' => Yii::t('gestion', 'Cerrar la versión actualmente en edición')] // Button
                    );
                }

                return '';
            },
            'contentOptions' => ['style' => 'text-align: center;'],
            'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Celdas'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Cantidad de preguntas contestadas'),
                'style' => 'text-align: center;',
            ],
            'attribute' => 'celdas',
            'contentOptions' => [
                'style' => 'text-align: center;',
                'title' => Yii::t('gestion', 'Cantidad de celdas rellenas'),
            ],
            // 'format' => 'html',
        ], [
            'label' => Yii::t('gestion', 'Coordinadores'),
            'headerOptions' => [  // HTML attributes for the header cell tag
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => Yii::t('gestion', 'Coordinadores del estudio'),
            ],
            'attribute' => 'coordinadores',
            // 'format' => 'html',
            'format' => 'email', // See http://www.yiiframework.com/doc-2.0/guide-output-formatting.html
        ],
    ],
    'options' => ['class' => 'cabecera-azul'],
    // 'summary' => '', // Do not show `Showing 1-19 of 19 items'.
    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
]);
?>
</div> <!-- table-responsive -->
<?php \yii\widgets\Pjax::end();
