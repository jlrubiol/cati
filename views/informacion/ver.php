<?php

use yii\helpers\Html;
use app\assets\VerticalTabsAsset;

$bundle = VerticalTabsAsset::register($this);

$this->title = Yii::t('cati', 'Información') . ' — ' . $estudio->nombre;

$this->params['breadcrumbs'][] = ['label' => $estudio->nombre, 'url' => ['estudio/ver', 'id' => $estudio->id_nk]];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Información');
?>

<h1><?php echo Html::encode($this->title); ?></h1>

<hr><br>


<div class="container">

<div class="col-xs-3"> <!-- required for floating -->
    <!-- Nav tabs -->
    <?php echo $this->render('_tabs'); ?>
</div>

<div class="col-xs-9">
    <!-- Tab panes -->
    <?php echo $this->render('_tabpanes', [
        'estudio' => $estudio,
        'pag1' => $pag1,
        'pag2' => $pag2,
        'pag3' => $pag3,
        'pag4' => $pag4,
        'pag5' => $pag5,
        'pag6' => $pag6,
        'pag7' => $pag7,
    ]); ?>
</div> <!-- col-xs-9 -->

</div> <!-- container -->
