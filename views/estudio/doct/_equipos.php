<?php
use yii\helpers\Html;

/*
 * Los datos de los equipos de investigación proceden de People
 * por medio de la pasarela `equipos_investigacion.kjb`.
 */
printf("<h2>%s</h2>\n", Yii::t('doct', 'Equipos de investigación'));

foreach ($nombres_equipos as $nombre_equipo) {
    echo "<h3>{$nombre_equipo['nombre_equipo']}</h3>";
    echo "<ul class='listado'>";
    $miembros = array_filter($miembros_equipos, function ($miembro) use ($nombre_equipo) {
        return $miembro['nombre_equipo'] === $nombre_equipo['nombre_equipo'];
    });
    foreach ($miembros as $miembro) {
        $urlCv = $miembro->getUrlCv();
        if ($urlCv && $urlCv != ' ') {
            echo "<li>" . Html::a(
                Html::encode("{$miembro['nombre']} {$miembro['apellido1']} {$miembro['apellido2']}"),
                $urlCv
            ) . " <span class='glyphicon glyphicon-link'></span></li>\n";
        } else {
            echo "<li>" . Html::encode("{$miembro['nombre']} {$miembro['apellido1']} {$miembro['apellido2']}") . "</li>";
        }
    }
    echo '</ul>';
}

echo "<br style='clear: both'>\n";

/*
echo "<ul class='listado'>\n";
echo '<li>' . Html::a(
    Yii::t('doct', 'Perfil (CV) del PDI'),
    'https://janovas.unizar.es/sideral/CV/busqueda?lang=' . Yii::$app->language
) . "</li>\n";
echo "</ul>\n";

echo "<br style='clear: both'>\n";
*/