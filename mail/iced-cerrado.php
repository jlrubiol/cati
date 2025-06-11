<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */

?>

<h2>Informe de la calidad de los Estudios de Doctorado y de sus diferentes programas</h2>

<dl>
  <dt>Curso:</dt>   <dd><?php echo $anyo; ?>/<?php echo $anyo + 1; ?></dd>
  <dt>Versión:</dt> <dd><?php echo $nombre_nueva_version; ?></dd>
  <dt>Enlace:</dt>  <dd><?php echo Html::a($url_pdf, $url_pdf); ?></dd>
</dl>

<p>Se remite adjunta la versión <?php echo $nombre_nueva_version; ?> del Informe de la calidad de los
Estudios de Doctorado y de sus diferentes programas (ICED).</p>

<p>El documento se puede obtener también con el enlace indicado anteriormente.</p>

<p>Por favor, no responda a este mensaje.</p>

<p>Cualquier cuestión puede tramitarla mediante el gestor de incidencias:
<?php echo Html::a('CAU', 'https://cau.unizar.es'); ?></p>

<p>Un cordial saludo<br>
 &nbsp;  El/La Director/a de la Escuela de Doctorado</p>
