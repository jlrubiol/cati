<?php

use yii\helpers\Html;

$this->title = Yii::t('gestion', 'Ver correos de los presidentes de las Comisiones de Garantía');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['//gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
echo join(',<br>', $presidentes);
?>
