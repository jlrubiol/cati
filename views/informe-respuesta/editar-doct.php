<?php
/**
 * Vista de la edición de una respuesta a una pregunta del Informe de evaluación de doctorado
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;
use yii\helpers\Url;
use marqu3s\summernote\Summernote;
use app\assets\ChartJsAsset;

$bundle = ChartJsAsset::register($this);

$this->title = Yii::t('cati', 'Editar respuesta');
$this->params['breadcrumbs'][] = [
    'label' => $respuesta->estudio->nombre,
    'url' => [$estudio->getMetodoVerEstudio(), 'id' => $respuesta->estudio->id_nk, 'anyo' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('cati', 'Informe de evaluación de la calidad'),
    'url' => ['informe/ver-doct', 'estudio_id' => $estudio->id, 'anyo' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Editar');

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<script src="<?php echo $bundle->baseUrl; ?>/Chart.bundle.js"></script>
<?php echo $this->render('@app/views/informe/_chart_config'); ?>

<h1><?php echo Html::encode($this->title); ?></h1>
<h2><?php echo Yii::t('cati', 'Curso') . ' ' . $respuesta->anyo . '/' . ($respuesta->anyo + 1); ?></h2>
<hr><br>

<?php echo $this->render('_formulario-doct', [
    'anyo' => $anyo,
    'datos' => $datos,
    'encuestas' => $encuestas,
    'estudio' => $estudio,
    'estudio_id_nk' => $estudio->id_nk,
    'pregunta' => $pregunta,
    'respuesta' => $respuesta,
    'ultimos_datos' => $ultimos_datos,
]); ?>
