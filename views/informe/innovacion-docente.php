<?php
/**
 * Vista de la página de la participación del profesorado de un estudio en proyectos de innovación docente.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;

$this->title = Yii::t('cati', 'Participación en proyectos de innovación docente') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Participación en proyectos de innovación docente');
?>

<h1><?php echo Html::encode($this->title); ?></h1>

<hr>

<?php
echo $this->render('_innovacion-docente', [
    'apartado' => $apartado ?? null,
    'estudio' => $estudio,
    'indos' => $indos,
    'num_tabla' => $num_tabla ?? null,
]);
