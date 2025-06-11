<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\Doctorado $model
*/

$this->title = Yii::t('models', 'Doctorado');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Doctorados'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud doctorado-create">

    <h1>
        <?= Yii::t('models', 'Doctorado') ?>
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
