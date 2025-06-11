<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\web\NotFoundHttpException;

switch ($tipo) {
    case 'grado-master':
        $nombre_lista = Yii::t('gestion', 'Informes de Grado y Máster');
        break;
    case 'doctorado':
        $nombre_lista = Yii::t('gestion', 'Informes de Doctorado');
        break;
    default:
        throw new NotFoundHttpException(sprintf(
            Yii::t('cati', 'No se ha encontrado el tipo de estudio «%s».  ☹'),
            $tipo
        ));
}
$this->title = $pregunta->apartado . ' ' . $pregunta->titulo;
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => $nombre_lista . ' ' . $anyo . '/' . ($anyo + 1),
    'url' => ['gestion/lista-informes', 'anyo' => $anyo, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Extractos'),
    'url' => ['gestion/seleccionar-pregunta', 'anyo' => $anyo, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
// echo '<p>'.HtmlPurifier::process($pregunta->pregunta)."</p>\n";

if ($pregunta->explicacion) {
    echo "<p class='alert alert-info'>\n";
    echo "<span class='glyphicon glyphicon-info-sign'></span> " . HtmlPurifier::process($pregunta->explicacion) . "\n";
    echo "</p>\n";
}

foreach ($respuestas as $respuesta) {
    echo '<h2>' . $respuesta->estudio->nombre . "</h2>\n";

    echo HtmlPurifier::process($respuesta->contenido);
    echo "<hr>\n\n";
}
