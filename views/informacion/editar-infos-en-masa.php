<?php

use app\models\Estudio;
use app\models\Informacion;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('cati', 'Edición de la información') . ' — ' . $tipoEstudio->nombre;
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
// Títulos de las solapas ("páginas")
if (in_array($tipoEstudio->id, [Estudio::GRADO_TIPO_ESTUDIO_ID, Estudio::MASTER_TIPO_ESTUDIO_ID])) {
    // Grado/Máster
    $titulos = [
        null,
        Yii::t('cati', 'Inicio'),
        Yii::t('cati', 'Acceso y admisión'),
        Yii::t('cati', 'Perfiles de salida'),
        Yii::t('cati', 'Qué se aprende'),
        Yii::t('cati', 'Plan de estudios'),
        Yii::t('cati', 'Apoyo al estudiante'),
        Yii::t('cati', 'Profesorado'),
    ];
} else {
    // Doctorado
    // Modificar también `views/estudio/doct/_tabs_doct.php`.
    $titulos = [
        null,
        Yii::t('cati', 'Información general'),
        Yii::t('cati', 'Competencias'),
        Yii::t('cati', 'Acceso, admisión y matrícula'),
        Yii::t('cati', 'Supervisión y seguimiento'),
        Yii::t('cati', 'Actividades formativas y movilidad'),
        Yii::t('cati', 'Profesorado. Líneas y equipos de investigación'),
        Yii::t('cati', 'Recursos y planificación'),
    ];
}

foreach ($paginas as $pagina => $secciones) {
    echo '<h2>' . $titulos[$pagina] . "</h2>\n";
    echo "<ul class='listado'>\n";
    foreach ($secciones as $seccion) {
        if (in_array($seccion->id, Informacion::SECCIONES_RESTRINGIDAS)) {
            echo '<li>' . Html::a(
                Yii::t('db', $seccion->titulo),
                [
                    'informacion/editar-en-masa',
                    'tipoEstudio_id' => $tipoEstudio->id,
                    'seccion_id' => $seccion->id,
                ]
            ) . "</li>\n";
        }
    }
    echo "</ul>\n\n";
}
?>
