<?php
/**
 * Vista para añadir un registro a un plan de innovación y mejora.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('cati', 'Añadir registro');
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => [$estudio->getMetodoVerEstudio(), 'id' => $estudio->id_nk, 'anyo' => $estudio->anyo_academico]
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('cati', 'Plan de mejora'),
    'url' => ['plan-mejora/ver', 'estudio_id' => $estudio->id, 'anyo' => $pregunta->anyo],
];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);

?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php echo $this->render('_formulario', [
    'respuesta' => $respuesta,
    'estudio' => $estudio,
    'pregunta' => $pregunta,
]); ?>