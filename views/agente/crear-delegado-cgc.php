<?php
/**
 * Vista para añadir un delegado del presidente de la CGC a un plan de estudios.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see     https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;

$this->title = Yii::t('cati', 'Añadir delegado del presidente de la CGC');
if (Yii::$app->user->can('unidadCalidad')) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['gestion/calidad']];
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Delegados presidentes CGC'), 'url' => ['agente/lista-delegados-cgc']];
} else {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['gestion/index']];
}
$this->params['breadcrumbs'][] = sprintf(Yii::t('gestion', 'Delegados del presidente de la CGC del plan %d'), $plan->id_nk);
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
$msg = "En esta página puede añadir un delegado del presidente de la CGC.\n"
  . 'Un delegado ayuda al presidente de la CGC del plan a <b>gestionar la web de estudios</b>'
  . " (informes de evaluación, planes de innovación y mejora, etc).\n"
  . 'Los delegados tienen <b>los mismos permisos</b> en esta web que el propio presidente.';
Yii::$app->session->setFlash('info', $msg);

echo $this->render('_formulario_delegado', [
    'model' => $model,
    'plan' => $plan,
]); ?>
