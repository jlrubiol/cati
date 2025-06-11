<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */

?>

<h2>Informe de Evaluación de la Calidad</h2>

<dl>
  <dt>Estudio:</dt> <dd><?php echo $estudio->nombre; ?> (cód. <?php echo $estudio->id_nk; ?>)</dd>
  <dt>Curso:</dt>   <dd><?php echo $anyo; ?>/<?php echo $anyo + 1; ?></dd>
  <dt>Enlace:</dt>  <dd><?php echo Html::a($url_pdf, $url_pdf); ?></dd>
</dl>

<p>Se remite adjunto el Informe de Evaluación de la Calidad.</p>

<p>El documento se puede obtener también con el enlace indicado anteriormente.</p>

<p>Por favor, no responda a este mensaje.</p>

<p>Cualquier cuestión puede tramitarla mediante el gestor de incidencias:
<?php echo Html::a('CAU', 'https://cau.unizar.es'); ?></p>

<p>Un cordial saludo<br>
 &nbsp;  El/La Director/a de la Escuela de Doctorado</p>
