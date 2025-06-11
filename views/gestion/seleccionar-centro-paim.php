<?php
/**
 * Vista para seleccionar un centro del que mostrar las acciones de
 * los planes de innovación y mejora de todos los estudios del centro.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Acciones de los planes de innovación y mejora por centro');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="menu-centros" data-toggle="dropdown">
        <?php echo Yii::t('gestion', 'Seleccione un centro'); ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu-centros">
        <?php
        foreach ($centros as $centro) {
            echo '<li role="presentation">' . Html::a(
                HtmlPurifier::process($centro->nombre),
                ['extractos-paim-centro', 'anyo' => $anyo, 'centro_id' => $centro->id],
                ['role' => 'menuitem']
            ) . "</li>\n";
        }
        ?>
    </ul>
</div>
