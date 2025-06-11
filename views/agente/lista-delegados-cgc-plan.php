<?php
/**
 * Vista de la lista de los delegados del presidente de la CGC de un plan de estudios.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;

$this->title = sprintf(Yii::t('gestion', 'Delegados del presidente de la CGC del plan %d'), $plan->id_nk);
if (Yii::$app->user->can('unidadCalidad')) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/calidad']];
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Delegados presidentes CGC'), 'url' => ['//agente/lista-delegados-cgc']];
} else {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/index']];
}
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<h2><?php echo $plan->estudio->nombre; ?></h2>
<h3><?php echo $plan->centro->nombre; ?></h3>
<hr><br>

<?php
$msg = "En esta página puede gestionar los delegados del presidente de la Comisión de Garantía de la Calidad.\n"
  . 'Un delegado es una persona que ayuda al presidente de la CGC a <b>gestionar la web de estudios</b>'
  . " (informes de evaluación, planes de innovación y mejora, etc).\n"
  . 'Los delegados tienen <b>los mismos permisos</b> en esta web que el propio presidente.';
echo \yii\bootstrap\Alert::widget([
    'body' => "<span class='glyphicon glyphicon-info-sign'></span>" . nl2br($msg),  // Html::encode($msg)),
    'options' => ['class' => 'alert-info'],
]) . "\n\n";

\yii\widgets\Pjax::begin([
    'id' => 'pjax-main',
    'enableReplaceState' => false,
    'linkSelector' => '#pjax-main ul.pagination a, th a',
    'clientOptions' => ['pjax:success' => 'function() { alert("yo"); }']
]);

echo Html::a(
    '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('cruds', 'Nuevo delegado'),
    ['crear-delegado-cgc', 'plan_id' => $plan->id],
    ['class' => 'btn btn-info']
); ?><br><br>

<div class='table-responsive'>
    <?php
    $dataProvider = new ArrayDataProvider([
        'allModels' => $delegados_cgc,
        'pagination' => false,  // ['pageSize' => 10],
        'sort' => [
            'attributes' => ['nombre', 'apellido1', 'apellido2', 'email'],
            'defaultOrder' => [
                'nombre' => SORT_ASC,
                'apellido1' => SORT_ASC,
                'apellido2' => SORT_ASC,
            ],
        ],
    ]);

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'nombre',
            'apellido1',
            'apellido2',
            'nip',
            [
                'attribute' => 'email',
                'format' => 'email',  // See http://www.yiiframework.com/doc-2.0/guide-output-formatting.html
            ], [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'editar' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('gestion', 'Editar el delegado'),
                            'aria-label' => Yii::t('gestion', 'Editar el delegado'),
                            'data-pjax' => '0',
                        ];

                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                    },
                    'borrar' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('gestion', 'Borrar el delegado'),
                            'aria-label' => Yii::t('gestion', 'Borrar el delegado'),
                            'data-confirm' => Yii::t('gestion', '¿Seguro que desea eliminar este delegado?'),
                            'data-pjax' => '0',
                        ];

                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                    },
                ],
                // 'controller' => 'gestion',
                'template' => '{editar} {borrar}',
                'urlCreator' => function ($action, $model, $key, $index) use ($plan) {
                    $params = ["{$action}-delegado-cgc", 'id' => $model->id, 'plan_id' => $plan->id];

                    return Url::toRoute($params);
                },
                // visibleButtons => ...,
                'contentOptions' => ['nowrap' => 'nowrap'],
            ],
        ],
        'options' => ['class' => 'cabecera-azul'],
        // 'summary' => '', // Do not show `Showing 1-19 of 19 items'.
        'tableOptions' => ['class' => 'table table-striped table-hover'],
    ]);
    ?>
</div>

<?php \yii\widgets\Pjax::end() ?>