<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\AcreditacionEstudio $model
*/

$this->title = Yii::t('models', 'Acreditacion Estudio');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Acreditacion Estudio'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->nk, 'url' => ['view', 'nk' => $model->nk]];
$this->params['breadcrumbs'][] = Yii::t('cruds', 'Edit');
?>
<div class="giiant-crud acreditacion-estudio-update">

    <h1>
        <?= Yii::t('models', 'Acreditacion Estudio') ?>
        <small>
                        <?= Html::encode($model->nk) ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?= Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('cruds', 'View'), ['view', 'nk' => $model->nk], ['class' => 'btn btn-default']) ?>
    </div>

    <hr />

    <?php echo $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
