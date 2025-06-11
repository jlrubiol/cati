<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\InformeRespuesta $model
*/

$this->title = Yii::t('cruds', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Informe Respuestas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud informe-respuesta-create">

    <h1>
        <?= Yii::t('models', 'Informe Respuesta') ?>
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
