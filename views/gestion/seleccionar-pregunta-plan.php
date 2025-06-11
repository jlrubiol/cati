<?php
/**
 * Vista para seleccionar un apartado de los planes de innovación y mejora
 * del que mostrar las acciones de todos los estudios.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Acciones de los planes de innovación y mejora');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('gestion', 'Planes de innovación y mejora') . ' ' . $anyo . '/' . ($anyo + 1),
    'url' => ['gestion/lista-planes', 'anyo' => $anyo, 'tipo' => $tipo],
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
                ['extractos-plan', 'anyo' => $anyo, 'pregunta_id' => $pregunta->id, 'tipo' => $tipo],
                ['role' => 'menuitem']
            ) . "</li>\n";
        }
        ?>
    </ul>
</div>
