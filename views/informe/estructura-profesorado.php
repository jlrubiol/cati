<?php
/**
 * Vista de la estructura del profesorado.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;

$this->title = Yii::t('cati', 'Tabla de estructura del profesorado') . ' ' . $anyo . '/' . ($anyo + 1);
$this->params['breadcrumbs'][] = [
    'label' => $nombre_estudio,
    'url' => ['estudio/ver', 'id' => $estudio_id_nk, 'anyo_academico' => $anyo]
];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?php echo Html::encode($this->title); ?> &nbsp; 
<a href="<?php echo Yii::getAlias('@web') ?>/pdf/definiciones_web_estudios_profesorado_v10.pdf"><span class="icon-info-with-circle"></span></a></h1>
<hr>

<?php
echo $this->render('_estructura_profesorado', [
    'apartado' => $apartado ?? null,
    'anyo' => $anyo,
    'estudio_id_nk' => $estudio_id_nk,
    'estructuras' => $estructuras,
    'nombre_estudio' => $nombre_estudio,
    'num_tabla' => $num_tabla ?? null,
]);
