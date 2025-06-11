<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\InformePreguntaLang $model
*/

$this->title = Yii::t('models', 'Informe Pregunta Lang');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Informe Pregunta Langs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud informe-pregunta-lang-create">

    <h1>
        <?= Yii::t('models', 'Informe Pregunta Lang') ?>
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
