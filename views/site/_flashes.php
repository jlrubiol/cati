<?php
/**
 * Fragmento de vista que muestra los mensajes Flash de una sesión.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\bootstrap\Alert;
use yii\helpers\Html;

if (Yii::$app->session->hasFlash('error')) {
    foreach (Yii::$app->session->getFlash('error') as $msg) {
        echo Alert::widget([
            'body' => "<span class='glyphicon glyphicon-remove-sign'></span>".nl2br(Html::encode($msg)),
            'options' => ['class' => 'alert-danger'],
        ])."\n\n";
    }
}

if (Yii::$app->session->hasFlash('warning')) {
    foreach (Yii::$app->session->getFlash('warning') as $msg) {
        echo Alert::widget([
            'body' => "<span class='glyphicon glyphicon-exclamation-sign'></span>".nl2br(Html::encode($msg)),
            'options' => ['class' => 'alert-warning'],
        ])."\n\n";
    }
}

if (Yii::$app->session->hasFlash('info')) {
    foreach (Yii::$app->session->getFlash('info') as $msg) {
        echo Alert::widget([
            'body' => "<span class='glyphicon glyphicon-info-sign'></span>".nl2br(Html::encode($msg)),
            'options' => ['class' => 'alert-info'],
        ])."\n\n";
    }
}

if (Yii::$app->session->hasFlash('success')) {
    foreach (Yii::$app->session->getFlash('success') as $msg) {
        echo Alert::widget([
            'body' => "<span class='glyphicon glyphicon-ok-sign'></span>".nl2br(Html::encode($msg)),
            'options' => ['class' => 'alert-success'],
        ])."\n\n";
    }
}
