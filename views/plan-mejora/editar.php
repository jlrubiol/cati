<?php
/**
 * Vista de la edición de un registro de un plan de innovación y mejora.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('cati', 'Editar registro');
$this->params['breadcrumbs'][] = [
    'label' => $respuesta->estudio->nombre,
    'url' => [$estudio->getMetodoVerEstudio(), 'id' => $respuesta->estudio_id_nk, 'anyo' => $respuesta->anyo],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('cati', 'Plan de mejora'),
    'url' => ['plan-mejora/ver', 'estudio_id' => $respuesta->estudio_id, 'anyo' => $respuesta->anyo],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Editar');

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);

$pregunta = $respuesta->getPlanPregunta()->one();
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php echo $this->render('_formulario', [
    'respuesta' => $respuesta,
    'estudio' => $estudio,
    'pregunta' => $pregunta,
]); ?>
