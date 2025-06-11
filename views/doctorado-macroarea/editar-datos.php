<?php

use yii\helpers\Html;
use app\models\User;

$this->title = Yii::t('cati', 'Editar datos');

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('cati', 'Informe de la calidad de los estudios de doctorado'),
    'url' => ['informe/ver-iced', 'anyo' => $anyo],
];
$this->params['breadcrumbs'][] = $this->title;

// Cambiar color de fondo
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php echo $this->render('_formulario', ['model' => $model]);?>
