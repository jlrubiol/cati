<?php
/**
 * Página de gestión de los estudios para los coordinadores.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use app\models\Agente;
use app\models\Calendario;
use app\models\Estudio;
use app\models\InformePublicado;
use app\models\PlanPublicado;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('gestion', 'Mis estudios');
$this->params['breadcrumbs'][] = $this->title;

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);

$language = Yii::$app->language;
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<hr><br>

<?php
foreach ($estudios as $estudio) {
    if ($estudio->id_nk === Estudio::ICED_ESTUDIO_ID || !$estudio->activo) {
        continue;
    }
    $anyo_academico = $estudio->anyo_academico;
    $anterior_anyo_academico = $anyo_academico - 1;
    $anyo_siguiente = $anyo_academico + 1;
    try {
        $estudio_anterior = Estudio::getEstudioByNk($anyo_academico - 1, $estudio->id_nk);
    } catch(Exception $e) {
        $estudio_anterior = null;
        $informe_publicado = null;
        $plan_publicado = null;
    }

    if (isset($estudio_anterior)) {
        $informe_publicado = InformePublicado::find()
            ->where(['estudio_id' => $estudio_anterior->id, 'language' => $language])->one();

        if (!$informe_publicado) {
            $informe_publicado = new InformePublicado(
                [
                    'estudio_id' => $estudio_anterior->id,
                    'anyo' => $estudio_anterior->anyo_academico,
                    'language' => $language,
                    'version' => 0,
                ]
            );
        }
        $version_maxima_informe = $informe_publicado->getVersionMaxima();
    }

    $funcion_ver = $estudio->getMetodoVerInforme();
    $funcion_editar = $estudio->getMetodoEditarInforme();
    $tipo = $estudio->getTipoEstudio();
    $version = isset($informe_publicado) ? $informe_publicado->version : 0;

    $esCoorDele = in_array($estudio->id_nk, $idNkEstudiosCoordinados); ?>

    <h2><?php echo $estudio->nombre; ?></h2>

    <ul class="listado">
    <?php

    /* Informe de evaluación del estudio */
    // --------------------------------------------------------------------------------
    $texto_enlace = Yii::t('gestion', 'Informe de Evaluación de la Calidad');

    echo "<li>\n";
    if (isset($estudio_anterior) and $version === $version_maxima_informe) {
        $pdffile = "informe-{$language}-{$estudio_anterior->id_nk}-v{$version}.pdf";
        echo Html::a(
            $texto_enlace . ' ' . ($anyo_academico - 1) . '/' . $anyo_academico,
            Url::base() . "/pdf/informes/{$estudio_anterior->anyo_academico}/{$pdffile}"
        );
    } else {
        echo '<li>' . $texto_enlace . ' ' . ($anyo_academico - 1) . '/' . $anyo_academico;
    }

    if (isset($estudio_anterior) and $version < $version_maxima_informe and \Yii::$app->user->can('editarInforme', ['estudio' => $estudio])) {
        echo ' &nbsp; ';
        echo Html::a(
            '<span class="glyphicon glyphicon-pencil"></span> '
              . Yii::t('gestion', 'Ver/Editar borrador'),
            [$funcion_ver, 'estudio_id' => $estudio_anterior->id, 'anyo' => $estudio_anterior->anyo_academico],
            [
                'id' => 'ver-informe',
                'class' => 'btn btn-info btn-xs',  // Button
            ]
        ) . "\n";
    }
    echo "</li>\n";

    /* Plan de innovación y mejora del estudio */
    // --------------------------------------------------------------------------------
    echo "<li>\n";
    if (isset($estudio_anterior)) {
        $plan_publicado = PlanPublicado::find()
            ->where(
                [
                    'estudio_id' => $estudio_anterior->id,
                    'language' => $language
                ]
            )->one();
        if (!$plan_publicado) {
            $plan_publicado = new PlanPublicado(
                [
                    'estudio_id' => $estudio_anterior->id,
                    'anyo' => $estudio_anterior->anyo_academico,
                    'language' => $language,
                    'version' => 0,
                ]
            );
        }
        $version = $plan_publicado->version;
        $version_maxima_plan = $plan_publicado->getVersionMaxima();
    }

    if (isset($estudio_anterior) and $version === $version_maxima_plan) {
        $pdffile = "plan-{$language}-{$estudio->id_nk}-v{$version}.pdf";
        echo Html::a(
            Yii::t('gestion', "Plan anual de innovación y mejora para el curso {$anyo_academico}/{$anyo_siguiente}"),
            Url::base() . "/pdf/planes-mejora/{$estudio_anterior->anyo_academico}/{$pdffile}"
        ) . "Campaña {$anyo_academico}\n";
    } else {
        echo Yii::t('gestion', "Plan anual de innovación y mejora para el curso {$anyo_academico}/{$anyo_siguiente}. Campaña {$anyo_academico}");
    }

    if (isset($estudio_anterior)
        and $version < $version_maxima_plan
        and \Yii::$app->user->can('editarPlan', ['estudio' => $estudio])
    ) {
        echo ' &nbsp; ';
        echo Html::a(
            '<span class="glyphicon glyphicon-pencil"></span> '
            . Yii::t('gestion', 'Ver/Editar borrador'),
            ['plan-mejora/ver', 'estudio_id' => $estudio_anterior->id, 'anyo' => $anterior_anyo_academico],
            [
                'id' => 'ver-plan',
                'class' => 'btn btn-info btn-xs',  // Button
            ]
        );
    }
    echo "</li>\n";


    /* Información de la titulación */
    // --------------------------------------------------------------------------------
    if ($esCoorDele) {
        echo '<li>' . Html::a(
            Yii::t('cati', 'Editar la información general'),
            [
                'informacion/editar-infos',
                'estudio_id' => $estudio->id,
                'tipo' => $tipo,
            ]
        ) . '</li>';
    }

    echo "</ul>\n\n";

    if ($esCoorDele) {
        /*
        * Recorrer los planes del estudio para mostrar enlaces para
        * - Ver/actualizar los delegados
        * - Ver/actualizar el horario (sólo para Grado y Máster)
        * - Ver/actualizar el enlace a la web del plan (sólo para Doctorado)
        */
        $planes_coordinados = [];
        if (array_key_exists($estudio->id, $planes)) {
            $planes_coordinados = $planes[$estudio->id];
        }
        foreach ($planes_coordinados as $plan) {
            ?>
            <h3>Plan <?php echo $plan->id_nk; ?></h3>
            <ul class="listado">
            <?php
            echo "<li>\n";
            echo Html::a(
                Yii::t('gestion', 'Ver/actualizar los delegados'),
                ['//agente/lista-delegados-plan', 'plan_id' => $plan->id]
            ) . ' (' . Yii::t('gestion', 'Personas que ayudan a gestionar esta web') . ')';
            echo "</li>\n";

            if ($estudio->esGradoOMaster()) {
                echo "<li>\n";
                echo Html::a(
                    Yii::t('gestion', 'Ver/actualizar el horario'),
                    ['gestion/ver-horario', 'id' => $plan->id]
                );
                echo "</li>\n";
            }

            if ($estudio->esDoctorado()) {
                echo "<li>\n";
                echo Html::a(
                    Yii::t('gestion', 'Ver/actualizar la dirección de la web específica del plan'),
                    ['gestion/ver-url-web-plan', 'id' => $plan->id]
                );
                echo "</li>\n";
            }

            echo "</ul>\n";
        }
    }
}  // foreach ($estudios as $estudio)


// Informe de la Calidad de los Estudios de Doctorado y de sus diferentes programas
// --------------------------------------------------------------------------------
$idNkEstudios = array_column($estudios, 'id_nk');
if (in_array(Estudio::ICED_ESTUDIO_ID, $idNkEstudios)) {
    $anyo_doctorado = Calendario::getAnyoDoctorado();
    $anterior_anyo_doctorado = $anyo_doctorado - 1;
    $estudio = Estudio::getEstudio(Estudio::ICED_ESTUDIO_ID);
    $informe_publicado = InformePublicado::find()
        ->where(['estudio_id' => $estudio->id, 'anyo' => $anterior_anyo_doctorado, 'language' => $language])->one();
    $version = isset($informe_publicado) ? $informe_publicado->version : 0; ?>

    <h2><?php echo Yii::t(
        'doct',
        'Informe de la Calidad de los Estudios de Doctorado y de sus diferentes Programas'
    ); ?></h2>

    <ul class="listado">
    <li>
    <?php
    if (InformePublicado::MAX_VERSION_INFORME_ICED === $version) {
        $pdffile = "iced-{$language}-v{$version}.pdf";
        echo Html::a(
            Yii::t(
                'gestion',
                'Informe'
            ) . ' ' . $anterior_anyo_doctorado . '/' . ($anyo_doctorado),
            Url::base() . "/pdf/informes/{$anterior_anyo_doctorado}/{$pdffile}"
        );
    } else {
        echo '<li>' . Yii::t(
            'gestion',
            'Informe'
        ) . ' ' . $anterior_anyo_doctorado . '/' . $anyo_doctorado;
    }

    if ($version < InformePublicado::MAX_VERSION_INFORME_ICED
        and \Yii::$app->user->can('editarInforme', ['estudio' => $estudio])
    ) {
        echo ' &nbsp; ';
        echo Html::a(
            '<span class="glyphicon glyphicon-pencil"></span> '
                . Yii::t('gestion', 'Ver/Editar borrador'),
            ['informe/ver-iced', 'anyo' => $anterior_anyo_doctorado],
            [
                'id' => 'ver-iced',
                'class' => 'btn btn-info btn-xs',  // Button
            ]
        ) . "\n";
    }
    echo "</li>\n";
    echo "</ul>\n";
}

// Acciones de los PAIM del centro de un decano/director
// --------------------------------------------------------------------------------
foreach ($centros_dirigidos as $centro) { ?>
    <h2><?php echo $centro->nombre; ?></h2>

    <ul class="listado">
        <li><?php echo Html::a(
            Yii::t('cati', "Acciones de los PAIM del centro para el curso {$anyo_academico}/{$anyo_siguiente}."),
            [
                'gestion/extractos-paim-centro',
                'anyo' => $anterior_anyo_academico,
                'centro_id' => $centro->id,
            ]
        ) . " Campaña {$anyo_academico}" ?></li>
    </ul>
<?php } ?>