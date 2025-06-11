<?php

printf("<h2>%s</h2>\n", Yii::t('doct', 'Líneas de investigación'));

echo "<ul class='listado'>";
foreach ($lineas as $linea) {
    echo "<li>{$linea->descripcion}</li>";
}
echo '</ul>';

echo "<br style='clear: both'>\n";
