<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?php echo nl2br(Html::encode($message)) ?>
    </div>

    <p><?php echo Yii::t('app', 'Se produjo el error superior mientras el servidor web procesaba su petición.') ?></p>

    <p><?php echo Yii::t('app', 'Contacte con nosotros si piensa que es un error del servidor. Gracias.') ?></p>

</div>
