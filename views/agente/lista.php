<?php
use app\models\User;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('cati', 'Agentes del Sistema de Gestión de la Calidad') . ' (' . $estudio->nombre . ')';
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Agentes del Sistema de Gestión de la Calidad');
?>

<h1><?php echo Html::encode($this->title); ?></h1>

<hr>

<?php
foreach ($datos as $centro => $comisiones) {
    ?>
    <h2><?php echo Html::encode($centro); ?></h2>
    <?php
    foreach ($comisiones as $comision => $dataProvider) {
        ?>
        <h3><?php echo Yii::t('db', $comision); ?></h3>
        <div class="table-responsive">
        <?php
        $mostrar_correos = Yii::$app->user->can('unidadCalidad');

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'rol',
                    'content' => function ($model) {
                        return Yii::t('db', $model->rol);
                    },
                    'headerOptions' => ['class' => 'col-sm-2'],
                ], [
                    'attribute' => 'nombre',
                    'headerOptions' => ['class' => 'col-sm-3'],
                ],
                'apellido1',
                'apellido2',
                [
                    'attribute' => 'email',
                    'visible' => $mostrar_correos,
                ],
            ],
            'options' => ['class' => 'cabecera-azul'],
            'summary' => '', // Do not show `Showing 1-19 of 19 items'.
            'tableOptions' => ['class' => 'table table-striped table-hover'],
        ]); ?>
        </div>
        <?php
    }  // foreach ($comisiones as $comision => $agentes)

    if ('' != $centro) {
        echo "<hr><br>\n";
    }
}  // foreach ($datos as $centro => $comisiones)
