<?php
/**
 * Vista para crear una nueva fecha clave.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;

/*
 * @var yii\web\View $this
 * @var app\models\Calendario $model
 */

$this->title = Yii::t('models', 'Nueva fecha');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Fechas clave'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1>
    <?php echo Yii::t('models', 'Fechas clave'); ?>
    <small><?php echo Html::encode($this->title); ?></small>
</h1>
<hr><br>

<?php
echo yii\bootstrap\Alert::widget([
    'body' => "<span class='glyphicon glyphicon-info-sign'></span>" . nl2br(Yii::t('gestion', 'La web de estudios pasará a mostrar la información del nuevo curso en la fecha que se indique.
    En <em>año</em> se debe indicar el de comienzo del curso.')),
    'options' => ['class' => 'alert-info'],
]) . "\n\n";

echo yii\bootstrap\Alert::widget([
    'body' => "<span class='glyphicon glyphicon-exclamation-sign'></span>" . nl2br(Yii::t('gestion', '<strong>Antes</strong> de cambiar el año, probablemente desee <strong>clonar los apartados</strong> tanto del IEC como del PAIM (y del ICED, en el caso de Doctorado).')),
    'options' => ['class' => 'alert-warning'],
]) . "\n\n";

echo yii\bootstrap\Alert::widget([
    'body' => "<span class='glyphicon glyphicon-warning-sign'></span>" . nl2br(Yii::t('gestion', 'Al cambiar el año académico de Grado y Máster o de Doctorado, <strong>contacte con el responsable de las pasarelas</strong> para que las actualice.')),
    'options' => ['class' => 'alert-warning'],
]) . "\n\n";

echo $this->render('_form', [
    'model' => $model,
]);