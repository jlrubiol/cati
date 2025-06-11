<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\NotasPlan $model
*/

$this->title = Yii::t('models', 'Notas Plan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Notas Plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud notas-plan-create">

    <h1>
        <?= Yii::t('models', 'Notas Plan') ?>
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
