<?php
/**
 * Vista de la página de los estudiantes en planes de movilidad de un estudio.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;

$this->title = Yii::t('cati', 'Estudiantes en planes de movilidad') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Estudiantes en planes de movilidad');
?>

<h1><?php echo Html::encode($this->title); ?></h1>

<hr>

<?php
echo $this->render('_movilidades', [
    'apartado' => $apartado ?? null,
    'estudio' => $estudio,
    'movilidades_in' => $movilidades_in,
    'movilidades_out' => $movilidades_out,
    'movilidad_porcentajes' => $movilidad_porcentajes,
    'num_tabla' => $num_tabla ?? null,
]);
