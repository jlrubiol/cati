<?php
use yii\helpers\Html;

$this->title = Yii::t('cati', 'Distribución de calificaciones') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Distribución de calificaciones');
?>

<h1><?php echo Html::encode($this->title); ?></h1>

<hr>

<?php
echo $this->render('_calificaciones', [
    'anyo' => $anyo,
    'apartado' => $apartado ?? null,
    'dpsCalificaciones' => $dpsCalificaciones,
    'estudio' => $estudio,
    'num_tabla' => $num_tabla ?? null,
]);
