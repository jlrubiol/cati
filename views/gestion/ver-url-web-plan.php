<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = Yii::t('gestion', 'Dirección de la web específica del plan');
if (Yii::$app->user->can('doctorado')) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['gestion/doctorado']];
    $this->params['breadcrumbs'][] = [
        'label' => Yii::t('gestion', 'Webs específicas'),
        'url' => ['gestion/ver-webs-especificas'],
    ];
} else {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['gestion/index']];
}
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<div style="width: 100%">
    <?php
    echo DetailView::widget([
        'model' => $plan,
        'attributes' => [
            [
                'attribute' => 'estudio_id',
                'captionOptions' => [
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'left',
                    'title' => Yii::t('gestion', 'Código del estudio'),
                ],
            ], [
                'attribute' => 'id_nk',
                // 'contentOptions' => ['class' => 'bg-red'], // HTML attributes to customize value tag
                'captionOptions' => [
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'left',
                    'title' => Yii::t('gestion', 'Código del plan'),
                ],  // HTML attributes to customize label tag
            ],
            [
                'attribute' => 'estudio.nombre',
                'label' => Yii::t('cruds', 'Estudio'),
            ], [
                'label' => Yii::t('cruds', 'Centro'),
                'value' => $plan->centro->nombre,
            ],
            'url_web_plan',
        ],
    ]);

    echo Html::a(
        '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('gestion', 'Actualizar'),
        ['actualizar-url-web-plan', 'id' => $plan->id],
        ['id' => 'actualizar-url-web-plan', 'class' => 'btn btn-info']  // Button
    ) . " &nbsp; \n";
    ?>
</div>
