<?php

use yii\helpers\Html;
use app\assets\ChartJsAsset;

$bundle = ChartJsAsset::register($this);

$this->title = Yii::t('cati', 'Resultados globales') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Resultados globales');

?>

<script src="<?php echo $bundle->baseUrl  ?>/Chart.bundle.js"></script>
<?php echo $this->render('_chart_config') ?>

<h1><?php echo Html::encode($this->title); ?></h1>

<hr>

<h2><?php echo Yii::t('cati', 'Oferta / Nuevo ingreso / Matrícula') ?> &nbsp; <a href="<?php echo Yii::getAlias('@web') ?>/pdf/definiciones_web_estudios_globales_v12.pdf"><span class="icon-info-with-circle"></span></a></h2>

<?php
echo $this->render(
    '_globales_nuevo_ingreso', [
        'estudio' => $estudio,
        'globales' => $globales,
        'centros' => $centros,
    ]
);
?>

<h2><?php echo Yii::t('cati', 'Créditos reconocidos') ?> &nbsp; <a href="<?php echo Yii::getAlias('@web') ?>/pdf/definiciones_web_estudios_globales_v12.pdf"><span class="icon-info-with-circle"></span></a></h2>

<?php
echo $this->render(
    '_globales_creditos', [
        'estudio' => $estudio,
        'globales' => $globales,
        'centros' => $centros,
    ]
);

if ($globales_definitivos) {
    ?>
    <h2><?php echo Yii::t('cati', 'Cursos de adaptación al grado') ?> &nbsp; <a href="<?php echo Yii::getAlias('@web') ?>/pdf/definiciones_web_estudios_globales_v12.pdf"><span class="icon-info-with-circle"></span></a></h2>

    <?php
    echo $this->render(
        '_globales_adaptacion', [
            'estudio' => $estudio,
            'globales' => $globales_definitivos,
            'centros' => $centros,
        ]
    ); ?>

    <h2><?php echo Yii::t('cati', 'Duración media graduados') ?> &nbsp; <a href="<?php echo Yii::getAlias('@web') ?>/pdf/definiciones_web_estudios_globales_v12.pdf"><span class="icon-info-with-circle"></span></a></h2>

    <?php
    echo $this->render(
        '_globales_duracion', [
            'estudio' => $estudio,
            'globales' => $globales_definitivos,
            'centros' => $centros,
        ]
    ); ?>

    <h2><?php echo Yii::t('cati', 'Tasas de éxito/rendimiento/eficiencia') ?> &nbsp; <a href="<?php echo Yii::getAlias('@web') ?>/pdf/definiciones_web_estudios_globales_v12.pdf"><span class="icon-info-with-circle"></span></a></h2>

    <?php
    echo $this->render(
        '_globales_exito', [
            'estudio' => $estudio,
            'globales' => $globales_definitivos,
            'centros' => $centros,
        ]
    ); ?>

    <h2><?php echo Yii::t('cati', 'Tasas de abandono/graduación') ?> &nbsp; <a href="<?php echo Yii::getAlias('@web') ?>/pdf/definiciones_web_estudios_globales_v12.pdf"><span class="icon-info-with-circle"></span></a></h2>

    <?php
    echo $this->render(
        '_globales_abandono', [
            'estudio' => $estudio,
            'globales' => $globales_definitivos,
            'globales_abandono' => $globales_abandono,
            'centros' => $centros,
        ]
    );
}
