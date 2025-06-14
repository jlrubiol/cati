<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\EstudioLang $model
*/

$this->title = Yii::t('models', 'Estudio Lang');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Estudio Lang'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('cruds', 'Edit');
?>
<div class="giiant-crud estudio-lang-update">

    <h1>
        <?= Yii::t('models', 'Estudio Lang') ?>
        <small>
                        <?= $model->id ?>
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
