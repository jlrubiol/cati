<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\Linea $model
*/

$this->title = Yii::t('models', 'Linea');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Lineas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud linea-create">

    <h1>
        <?= Yii::t('models', 'Linea') ?>
        <small>
                        <?= $model->id ?>
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
