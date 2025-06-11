<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */

?>

<h2>Informe de Evaluación de la Calidad</h2>

<ul>
  <li>Estudio: <?= $estudio->nombre ?> (cód. <?= $estudio->id_nk ?>)</li>
  <li>Curso: <?= $anyo ?>/<?= ($anyo+1) ?></li>
  <li>Versión: <?= $nombre_nueva_version ?></li>
  <li>Enlace: <?= Html::a($url_pdf, $url_pdf) ?></li>
</ul>

<p>Se remite adjunta la versión <?= $nombre_nueva_version ?> del Informe de Evaluación de la Calidad.</p>

<p>El documento se puede obtener también con el enlace indicado anteriormente.</p>

<p>Por favor, no responda a este mensaje.</p>

<p>Cualquier cuestión puede tramitarla mediante el gestor de incidencias:
<?= Html::a('CAU', 'https://cau.unizar.es') ?></p>

<p>Un cordial saludo<br>
    El/La Vicerrector/a de Política Académica</p>
