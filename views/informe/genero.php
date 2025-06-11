<?php
/**
 * Vista de la página de género de los estudiantes de un estudio.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 */
use yii\helpers\Html;

$this->title = Yii::t('cati', 'Perfil de ingreso de los estudiantes: género') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico]
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Perfil de ingreso de los estudiantes: género');
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr>

<?php
echo $this->render('_genero', [
    'apartado' => $apartado ?? null,
    'estudio' => $estudio,
    'generos' => $generos,
    'num_tabla' => $num_tabla ?? null,
]);