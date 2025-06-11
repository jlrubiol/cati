<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\AcreditacionEstudio $model
*/

$this->title = Yii::t('models', 'Acreditacion Estudio');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Acreditacion Estudios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud acreditacion-estudio-create">

    <h1>
        <?= Yii::t('models', 'Acreditacion Estudio') ?>
        <small>
                        <?= Html::encode($model->nk) ?>
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
