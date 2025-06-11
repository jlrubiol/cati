<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

switch ($tipo) {
    case 'grado-master':
        $nombre_lista = Yii::t('gestion', 'Informes de Grado y Máster');
        break;
    case 'doctorado':
        $nombre_lista = Yii::t('gestion', 'Informes de Doctorado');
        break;
    default:
        throw new NotFoundHttpException(Yii::t('cati', 'No se ha encontrado ese tipo de estudio.  ☹'));
}
$this->title = Yii::t('gestion', 'Extractos');

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Gestión'),
    'url' => ['//gestion/index'],
];
$this->params['breadcrumbs'][] = [
    'label' => $nombre_lista . ' ' . $anyo . '/' . ($anyo + 1),
    'url' => ['gestion/lista-informes', 'anyo' => $anyo, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="menu-preguntas" data-toggle="dropdown">
        <?php echo Yii::t('gestion', 'Seleccione una pregunta'); ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu-preguntas">
        <?php
        foreach ($preguntas as $pregunta) {
            echo '<li role="presentation">' . Html::a(
                HtmlPurifier::process($pregunta->apartado) . ' ' . HtmlPurifier::process($pregunta->titulo),
                ['extractos', 'anyo' => $anyo, 'pregunta_id' => $pregunta->id, 'tipo' => $tipo],
                ['role' => 'menuitem']
            ) . "</li>\n";
        }
        ?>
    </ul>
</div>
