<?php
/**
 * Vista de la página de las notas medias de admisión a un estudio.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;

$this->title = Yii::t('cati', 'Evolucion del PAS de apoyo a la docencia') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Evolucion del PAS de apoyo a la docencia');
?>

<h1><?php echo Html::encode($this->title); ?></h1>

<hr>

<?php
echo $this->render('_pas', [
    'apartado' => $apartado ?? null,
    'estudio' => $estudio,
    'evolucionesPas' => $evolucionesPas,
    'num_tabla' => $num_tabla ?? null,
]);
