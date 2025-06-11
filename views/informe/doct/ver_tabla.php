<?php
/**
 * Vista de una tabla de indicadores de doctorado.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;

$this->title = $caption . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver-doct', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = $caption;

require_once '_descripciones.php';

echo $this->render(
    $tabla,
    [
        'anyo' => $anyo,
        'caption' => $caption,
        'datos' => $datos,
        'descripciones' => $descripciones,
        'estudio' => $estudio,
        'titulo' => $titulo ?? $caption,
        'ultimos_datos' => $ultimos_datos,
    ]
);
