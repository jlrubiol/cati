<?php
/**
 * Vista para editar una fecha clave.
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

$this->title = Yii::t('models', 'Editar fecha');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Fechas clave'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Editar');

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1>
    <?php echo Yii::t('models', 'Fechas clave'); ?>
    <small><?php echo Html::encode($this->title); ?></small>
</h1>
<hr><br>

<?php echo $this->render('_form', [
    'model' => $model,
]); ?>
