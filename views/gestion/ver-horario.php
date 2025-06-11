<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = Yii::t('gestion', 'Horario');
if (Yii::$app->user->can('gradoMaster')) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/grado-master']];
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Horarios'), 'url' => ['//gestion/ver-horarios']];
} else {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['//gestion/index']];
}
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
$msg = "Algunos centros tienen publicados los horarios en la web del centro.\n"
    . 'En esta página puede introducir la dirección (URL) del horario'
    . ' para que se muestre un enlace a él en la página del estudio.';

echo \yii\bootstrap\Alert::widget([
    'body' => "<span class='glyphicon glyphicon-info-sign'></span>" . nl2br($msg),  // Html::encode($msg)),
    'options' => ['class' => 'alert-info'],
]) . "\n\n";
?>

<br>
<div style="width: 100%">
    <?php
    echo DetailView::widget([
        'model' => $plan,
        'attributes' => [
            [
                'attribute' => 'estudio.id_nk',
                'label' => Yii::t('cruds', 'Cód. estudio'),
                'captionOptions' => ['title' => Yii::t('gestion', 'Código del estudio')],
            ], [
                'attribute' => 'estudio.nombre',
                'label' => Yii::t('cruds', 'Estudio'),
            ], [
                'label' => Yii::t('cruds', 'Centro'),
                'value' => $plan->centro->nombre,
            ], [
                'attribute' => 'id_nk',
                // 'contentOptions' => ['class' => 'bg-red'], // HTML attributes to customize value tag
                'captionOptions' => [
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'left',
                    'title' => Yii::t('gestion', 'Código del plan'),
                ],  // HTML attributes to customize label tag
            ], [
                'label' => Yii::t('cati', 'URL del horario'),
                'value' => function ($plan) {
                    return Html::a(
                        $plan->url_horarios,
                        $plan->url_horarios
                    );
                },
                'format' => 'html',
            ],
        ],
        'options' => ['class' => 'table table-striped table-hover detail-view'],
    ]);

    echo Html::a(
        '<span class="glyphicon glyphicon-pencil"></span> ' . // Button
        Yii::t('gestion', 'Actualizar'),
        ['actualizar-horario', 'id' => $plan->id],
        [
            'id' => 'actualizar-horario',
            'class' => 'btn btn-info',
        ]
    ) . " &nbsp; \n";
    ?>
</div>

<?php
// La implementación actual de jQuery, bootstrap o del widget no funciona bien.
/*
$javascript = <<<JS
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
JS;
$this->registerJs($javascript);
*/
?>
