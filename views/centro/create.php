<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\Centro $model
*/

$this->title = Yii::t('models', 'Centro');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Centros'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud centro-create">

    <h1>
        <?= Yii::t('models', 'Centro') ?>
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
