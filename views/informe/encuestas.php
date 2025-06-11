<?php
/**
 * Vista de la página de las encuestas de satisfacción y egreso de un estudio.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;

$this->title = Yii::t('cati', 'Satisfacción y egreso') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Satisfacción y egreso');
?>

<h1><?php echo Html::encode($this->title); ?></h1>

<hr>

<?php
echo $this->render('_encuestas', [
    'apartado' => $apartado ?? null,
    'estudio' => $estudio,
    'encuestas' => $encuestas,
    'num_tabla' => $num_tabla ?? null,
]);
