<?php
/**
 * /home/quique/Devel/cati/cati/runtime/giiant/d4b4964a63cc95065fa0ae19074007ee
 *
 * @package default
 */


use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
 *
 * @var yii\web\View $this
 * @var app\models\InformacionLang $model
 */
$copyParams = $model->attributes;

$this->title = Yii::t('models', 'Informacion Lang');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Informacion Langs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('cruds', 'View');
?>
<div class="giiant-crud informacion-lang-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?php echo \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <h1>
        <?php echo Yii::t('models', 'Informacion Lang') ?>
        <small>
            <?php echo $model->id ?>
        </small>
    </h1>


    <div class="clearfix crud-navigation">

        <!-- menu buttons -->
        <div class='pull-left'>
            <?php echo Html::a(
	'<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('cruds', 'Edit'),
	[ 'update', 'id' => $model->id],
	['class' => 'btn btn-info']) ?>

            <?php echo Html::a(
	'<span class="glyphicon glyphicon-copy"></span> ' . Yii::t('cruds', 'Copy'),
	['create', 'id' => $model->id, 'InformacionLang'=>$copyParams],
	['class' => 'btn btn-success']) ?>

            <?php echo Html::a(
	'<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('cruds', 'New'),
	['create'],
	['class' => 'btn btn-success']) ?>
        </div>

        <div class="pull-right">
            <?php echo Html::a('<span class="glyphicon glyphicon-list"></span> '
	. Yii::t('cruds', 'Full list'), ['index'], ['class'=>'btn btn-default']) ?>
        </div>

    </div>

    <hr />

    <?php $this->beginBlock('app\models\InformacionLang'); ?>


    <?php echo DetailView::widget([
		'model' => $model,
		'attributes' => [
			// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
			[
				'format' => 'html',
				'attribute' => 'informacion_id',
				'value' => ($model->getInformacion()->one() ?
					Html::a('<i class="glyphicon glyphicon-list"></i>', ['informacion/index']).' '.
					Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> '.$model->getInformacion()->one()->id, ['informacion/view', 'id' => $model->getInformacion()->one()->id, ]).' '.
					Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'InformacionLang'=>['informacion_id' => $model->informacion_id]])
					:
					'<span class="label label-warning">?</span>'),
			],
			'texto:ntext',
			'language',
		],
	]); ?>


    <hr/>

    <?php echo Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('cruds', 'Delete'), ['delete', 'id' => $model->id],
	[
		'class' => 'btn btn-danger',
		'data-confirm' => '' . Yii::t('cruds', 'Are you sure to delete this item?') . '',
		'data-method' => 'post',
	]); ?>
    <?php $this->endBlock(); ?>



    <?php echo Tabs::widget(
	[
		'id' => 'relation-tabs',
		'encodeLabels' => false,
		'items' => [
			[
				'label'   => '<b class=""># '.$model->id.'</b>',
				'content' => $this->blocks['app\models\InformacionLang'],
				'active'  => true,
			],
		]
	]
);
?>
</div>
