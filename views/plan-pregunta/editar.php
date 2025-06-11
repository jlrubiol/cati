<?php
/**
 * Vista para editar un apartado de los planes de innovación y mejora.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;
use yii\helpers\Url;

$anyo = $pregunta->anyo;
$lista = 'gestion/lista-planes';
$nombre_lista = Yii::t('gestion', 'Planes de innovación y mejora');

$this->title = sprintf(Yii::t('cati', 'Edición de un apartado del plan %d/%d'), $anyo, $anyo + 1);

$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => $nombre_lista . ' ' . $anyo . '/' . ($anyo + 1),
    'url' => [$lista, 'anyo' => $anyo, 'tipo' => $pregunta->tipo],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('models', 'Apartados'),
    'url' => ['plan-pregunta/lista', 'anyo' => $anyo, 'tipo' => $pregunta->tipo],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Editar');

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php echo $this->render('_formulario', [
    'pregunta' => $pregunta,
]); ?>
