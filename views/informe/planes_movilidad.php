<?php
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
echo $this->render('_planes_movilidad', [
    'estudio' => $estudio,
    'dpMovilidades' => $dpMovilidades,
]);
