<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\PlanRespuestaLang $model
*/

$this->title = Yii::t('models', 'Plan Respuesta Lang');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Plan Respuesta Langs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud plan-respuesta-lang-create">

    <h1>
        <?= Yii::t('models', 'Plan Respuesta Lang') ?>
        <small>
                        <?= Html::encode($model->id) ?>
        </small>
    </h1>

    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?=             Html::a(
            Yii::t('cruds', 'Cancel'),
            \yii\helpers\Url::previous(),
            ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <hr />

    <?= $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
