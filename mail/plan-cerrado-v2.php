<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */

?>

<h2>Plan anual de innovación y mejora</h2>

<ul>
  <li>Estudio: <?= $estudio->nombre ?> (cód. <?= $estudio->id_nk ?>)</li>
  <li>Curso: <?= $anyo ?>/<?= ($anyo + 1) ?></li>
  <li>Versión: <?= $nombre_nueva_version ?></li>
  <li>Enlace: <?= Html::a($url_pdf, $url_pdf) ?></li>
</ul>

<p>Se remite adjunta la versión <?= $nombre_nueva_version ?> del plan anual de innovación y mejora
para su revisión y alegaciones en el plazo máximo de siete días hábiles.</p>

<p>Por favor, no responda a este mensaje.</p>

<p>Un cordial saludo<br>
    El/La Presidente/a de la Comisión de Garantía de la Calidad</p>
