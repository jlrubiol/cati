<?php


/*
 * Tasas de éxito/rendimiento/eficiencia
 * Vista incluída desde informe/ver
 */

echo $this->render('_globales_exito', [
    'estudio' => $estudio,
    'globales' => $globales,
    'centros' => $centros,
]);

/*
 * Tasas de abandono/graduación
 */

echo $this->render('_globales_abandono', [
    'estudio' => $estudio,
    'globales' => $globales,
    'centros' => $centros,
]);
