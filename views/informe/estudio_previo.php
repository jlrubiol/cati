<?php

use app\assets\ChartJsAsset;
use yii\helpers\Html;

$bundle = ChartJsAsset::register($this);

$this->title = Yii::t('cati', 'Estudio previo de los estudiantes de nuevo ingreso') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico]
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Estudio previo de los estudiantes de nuevo ingreso');
?>

<script src="<?php echo $bundle->baseUrl; ?>/Chart.bundle.js"></script>
<?php echo $this->render('_chart_config'); ?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr>

<?php

if ($estudio->esGrado()) { // Grado
    echo $this->render('_estudio_previo', [
        'apartado' => $apartado ?? null,
        'estudiosPrevios' => $estudiosPrevios,
        'estudio' => $estudio,
        'num_tabla' => $num_tabla ?? null,
    ]);
} else { // Máster
    echo $this->render('_estudio_previo_master', [
        'anyo' => $anyo,
        'apartado' => $apartado ?? null,
        'dpsEstudiosPrevios' => $dpsEstudiosPrevios,
        'estudio' => $estudio,
        'num_tabla' => $num_tabla ?? null,
    ]);
}
