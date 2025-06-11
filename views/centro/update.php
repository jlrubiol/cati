<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\Centro $model
*/

$this->title = Yii::t('models', 'Centro');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Centro'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('cruds', 'Edit');
?>
<div class="giiant-crud centro-update">

    <h1>
        <?= Yii::t('models', 'Centro') ?>
        <small>
                        <?= Html::encode($model->id) ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?= Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('cruds', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </div>

    <hr />

    <?php echo $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
