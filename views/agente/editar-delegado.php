<?php

use yii\helpers\Html;
use app\models\User;

$this->title = Yii::t('cati', 'Editar delegado del coordinador');

if (Yii::$app->user->can('unidadCalidad')) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['gestion/calidad']];
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Delegados coordinadores'), 'url' => ['agente/lista-delegados']];
} else {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['gestion/index']];
}
$this->params['breadcrumbs'][] = sprintf(Yii::t('gestion', 'Delegados del coordinador del plan %d'), $model->plan_id_nk);
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php echo $this->render('_formulario_delegado', ['model' => $model, 'plan' => $plan]); ?>
