<?php

use yii\helpers\Html;
use app\models\User;

$this->title = Yii::t('cati', 'Editar datos');

if (Yii::$app->user->can('unidadCalidad')) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['gestion/calidad']];
} else {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Gestión'), 'url' => ['gestion/index']];
}
$this->params['breadcrumbs'][] = [
    'label' => $estudio->nombre,
    'url' => ['estudio/ver-doct', 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico],
];
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php echo $this->render('_formulario', ['estudio' => $estudio, 'esCoorDele' => $esCoorDele, 'model' => $model]);?>
