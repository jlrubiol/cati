<?php
use yii\helpers\Html;

?>

<h2>
    <?php echo Yii::t('cati', 'Resultados académicos'); ?> <?php echo $year; ?>/<?php echo $year + 1; ?>
    (<a href="<?php echo Yii::getAlias('@web'); ?>/pdf/definiciones_web_estudios_curso_v11.pdf"><span class="icon-info-with-circle"></span></a>)
</h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('cati', 'Estudiantes en planes de movilidad'),
        ['informe/planes-movilidad', 'estudio_id' => $estudio->id, 'anyo' => $year]
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('cati', 'Análisis de los indicadores del título'),
        ['informe/indicadores', 'estudio_id' => $estudio->id, 'anyo' => $year]
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('cati', 'Distribución de calificaciones'),
        ['informe/calificaciones', 'estudio_id' => $estudio->id, 'anyo' => $year]
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('cati', 'Estudio previo de los estudiantes de nuevo ingreso'),
        ['informe/estudio-previo', 'estudio_id' => $estudio->id, 'anyo' => $year]
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('cati', 'Nota media de admisión'),
        ['informe/nota-media', 'estudio_id' => $estudio->id, 'anyo' => $year]
    ); ?></li>

    <li><?php echo Html::a(
        Yii::t('cati', 'Plazas de nuevo ingreso ofertadas'),
        ['informe/plazas-nuevo-ingreso', 'estudio_id' => $estudio->id, 'anyo' => $year]
    ); ?></li>
</ul>
