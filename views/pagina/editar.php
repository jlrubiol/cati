<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = sprintf(Yii::t('cati', 'Editar página %d'), $model->id);

$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Paginas'), 'url' => ['pagina/lista']];

$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php echo $this->render('_formulario', [
    'model' => $model,
]); ?>
