<?php
/**
 * Vista para crear un apartado de los planes de innovación y mejora.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;
use yii\helpers\Url;

$lista = 'gestion/lista-planes';
$nombre_lista = Yii::t('gestion', 'Planes de innovación y mejora');

$this->title = Yii::t('cati', 'Nuevo apartado');

$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => sprintf('%s %d/%d', $nombre_lista, $anyo, $anyo + 1),
    'url' => [$lista, 'anyo' => $anyo, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('models', 'Apartados'),
    'url' => ['plan-pregunta/lista', 'anyo' => $anyo, 'tipo' => $tipo],
];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php echo $this->render('_formulario', [
    'pregunta' => $pregunta,
    'tipo' => $tipo,
]); ?>
