<?php
/**
 * Vista para elegir qué sección de la información de un estudio se desea editar.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use yii\helpers\Html;
use yii\helpers\Url;

$funcion_ver = $estudio->getMetodoVerEstudio();

$this->title = Yii::t('cati', 'Edición de la información') . ' — ' . $estudio->nombre;
$this->params['breadcrumbs'][] = ['label' => $estudio->nombre, 'url' => [$funcion_ver, 'id' => $estudio->id_nk]];
$this->params['breadcrumbs'][] = Yii::t('cati', 'Editar información');

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
// Títulos de las solapas ("páginas")
// Al añadir/quitar/renombrar solapas, modificar también
// * estudios/`_tabs.php` o `_tabs_doct.php`
// * `editar-infos-en-masa.php`.
if ('grado-master' == $tipo) {
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
} else {  // doctorado
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

    if ($titulos[$pagina] == 'Profesorado. Líneas y equipos de investigación') {
        echo "<p>Las líneas de investigación se obtienen de Sigma, y los equipos de investigación de People.</p>";
        continue;
    }

    echo "<ul class='listado'>\n";
    foreach ($secciones as $seccion) {
        echo '<li>' . Html::a(
            Yii::t('db', $seccion->titulo),
            [
                'informacion/editar',
                'estudio_id' => $estudio->id,
                'seccion_id' => $seccion->id,
                'tipo' => $tipo,
            ],
            ['target' => '_blank']
        ) . "</li>\n";
    }
    echo "</ul>\n\n";
}
?>
