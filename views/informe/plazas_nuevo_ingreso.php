<?php
use yii\helpers\Html;

$this->title = Yii::t('cati', 'Plazas de nuevo ingreso ofertadas') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Plazas de nuevo ingreso ofertadas');
?>

<h1><?php echo Html::encode($this->title); ?></h1>

<hr>

<?php
echo $this->render('_plazas_nuevo_ingreso', [
    'estudio' => $estudio,
    'nuevos_ingresos' => $nuevos_ingresos,
]);
