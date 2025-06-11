<?php
use yii\helpers\Html;

$this->title = Yii::t('cati', 'Resultados académicos de años anteriores') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Resultados académicos de años anteriores');

?>

<h1><?php echo Html::encode($this->title); ?></h1>

<hr>

<?php

foreach ($anyos as $anyo) {
    echo $this->render('_resultados_academicos', [
        'estudio' => $estudio,
        'year' => $anyo,
    ]);
}
