<?php
use yii\helpers\Html;

$this->title = Yii::t('cati', 'Evolución del profesorado') . ' — ' . $nombre_estudio;
$this->params['breadcrumbs'][] = [
    'label' => $nombre_estudio,
    'url' => ['estudio/ver', 'id' => $estudio_id_nk]
];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Evolución del profesorado');

?>

<h1><?php echo Html::encode($this->title); ?> &nbsp; 
<a href="<?php echo Yii::getAlias('@web') ?>/pdf/definiciones_web_estudios_profesorado_v10.pdf"><span class="icon-info-with-circle"></span></a></h1>

<hr>

<?php
echo $this->render('_evolucion_profesorado', [
    'apartado' => $apartado ?? null,
    'evoluciones' => $evoluciones,
    'nombre_estudio' => $nombre_estudio,
    'num_tabla' => $num_tabla ?? null,
]);
