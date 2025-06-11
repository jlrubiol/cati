<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\Equipo $model
*/

$this->title = Yii::t('models', 'Equipo');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Equipos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud equipo-create">

    <h1>
        <?= Yii::t('models', 'Equipo') ?>
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
