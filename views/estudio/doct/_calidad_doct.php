<?php
use app\models\InformePublicado;
use app\models\PlanPublicado;
use yii\helpers\Html;
use yii\helpers\Url;

$language = Yii::$app->language;
$anterior_anyo_academico = $anyo_academico - 1;

$dir_informes = Yii::getAlias('@webroot') . "/pdf/informes/{$anterior_anyo_academico}";
$dir_paim = Yii::getAlias('@webroot') . "/pdf/planes-mejora/{$anterior_anyo_academico}";

$fichero_informe = "informe-{$language}-{$estudio->id_nk}-v{$version_informe}.pdf";
$fichero_paim = "plan-{$language}-{$estudio->id_nk}-v{$version_plan}.pdf";
$max_version_iced = InformePublicado::MAX_VERSION_INFORME_ICED;
$fichero_iced = "iced-{$language}-v{$max_version_iced}.pdf";

$hay_informe = file_exists("{$dir_informes}/{$fichero_informe}");
$hay_paim = file_exists("{$dir_paim}/{$fichero_paim}");
$hay_iced = file_exists("{$dir_informes}/{$fichero_iced}");

# INDICADORES 2013/2014 a 2016-2017
# $dir_indicadores = Yii::getAlias('@webroot') . '/pdf/indicadores';
# $fichero_indicadores = "indicadores-{$estudio->id_nk}.pdf";
# $hay_indicadores = file_exists("{$dir_indicadores}/{$fichero_indicadores}");

$url_ofiplan = 'http://academico.unizar.es/ofiplan';
$rama_ofiplan = [
    'H' => 'programas-de-doctorado-artes-y-humanidades',
    'J' => 'programas-de-doctorado-ciencias-sociales-y-juridicas',
    'S' => 'programas-de-doctorado-ciencias-de-la-salud',
    'T' => 'programas-de-doctorado-ingenieria-y-arquitectura',
    'X' => 'programas-de-doctorado-ciencias',
];
$url_memoria = "{$url_ofiplan}/{$rama_ofiplan[$estudio->rama_id]}";
?>

<!-- Normativa -->
<h2><?php echo Yii::t('cati', 'Normativa'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('cati', 'Cómo se asegura la calidad'),
        ['pagina/ver', 'id' => 9]
); ?></li>
    <li><?php echo Html::a(
        Yii::t('cati', 'Procedimientos del sistema interno de gestión de la calidad'),
        ['pagina/ver', 'id' => 10]
    ); ?></li>
</ul>


<!-- Documentos -->
<h2><?php echo Yii::t('cati', 'Documentos'); ?></h2>

<ul class="listado">
    <li>
        <?php
        if (InformePublicado::MAX_VERSION_INFORME_DOCT <= $version_informe and $hay_informe) {
            echo Html::a(
                Yii::t('cati', 'Informe de Evaluación de la Calidad (IEC) del curso') . " {$anterior_anyo_academico}/{$anyo_academico}",
                Url::home() . "pdf/informes/{$anterior_anyo_academico}/{$fichero_informe}"
            );
        } else {
            echo Yii::t('cati', 'Informe de Evaluación de la Calidad (IEC) del curso') . " {$anterior_anyo_academico}/{$anyo_academico}";
        }
        ?>
    </li>

    <li>
        <?php echo Html::a(
            Yii::t('cati', 'Acceso a los IEC de todos los cursos'),
            "http://zaguan.unizar.es/search?ln={$language}&cc=informe-autoevaluacion-calidad&sc=1&as=1&m1=a&p1={$estudio->id_nk}&f1=codigo_titulacion&op1=a&m2=a&p2=&f2=&op2=a&m3=a&p3=&f3=&action_search=Search&dt=&d1d=&d1m=&d1y=&d2d=&d2m=&d2y=&sf=&so=a&rm=&rg=10&of=hb",
            ['target' => '_blank']
        ). ' (' . Yii::t('cati', 'Repositorio institucional de documentos');
        ?>) <span class="glyphicon glyphicon-link"></span>
    </li>

    <!-- li>
        <?php
        /*
        if ($hay_indicadores) {
            echo Html::a(
                Yii::t('doct', 'Indicadores de calidad del programa, periodo 2013/2014 a 2016-2017'),
                Url::home() . "pdf/indicadores/{$fichero_indicadores}"
            );
        } else {
            echo Yii::t('doct', 'Indicadores de calidad del programa, periodo 2013/2014 a 2016-2017');
        }
        */
        ?>
    </li -->

    <li>
        <?php
        if ($hay_iced) {
            echo Html::a(
                Yii::t('doct', 'Informe de la Calidad de los Estudios de Doctorado y de sus diferentes Programas (ICED) del curso')
                  . " {$anterior_anyo_academico}/{$anyo_academico}",
                Url::home() . "pdf/informes/{$anterior_anyo_academico}/{$fichero_iced}"
            );
        } else {
            echo Yii::t('doct', 'Informe de la Calidad de los Estudios de Doctorado y de sus diferentes Programas (ICED) del curso')
              . " {$anterior_anyo_academico}/{$anyo_academico}";
        }
        ?>
    </li>

    <li>
        <?php echo Html::a(
            Yii::t('cati', 'Acceso a los ICED de todos los cursos'),
            "https://zaguan.unizar.es/collection/informe-calidad-estudios-doctorado?ln={$language}",
            ['target' => '_blank']
        ) . ' ' . Yii::t('cati', '(Repositorio institucional de documentos)'); ?> <span class="glyphicon glyphicon-link"></span>
    </li>

    <li>
        <?php
        $max_version_plan_doct = PlanPublicado::MAX_VERSION_PLAN_DOCT;
        # En el curso 2021-22, PlanPublicado::MAX_VERSION_PLAN_DOCT pasó de ser 1 a 2.
        if ($anterior_anyo_academico < 2021) {
            $max_version_plan_doct -= 1;
        }

        if ($max_version_plan_doct <= $version_plan and $hay_paim) {
            echo Html::a(
                Yii::t('cati', 'Plan anual de innovación y mejora (PAIM) del curso') . " {$anterior_anyo_academico}/{$anyo_academico}",
                Url::home() . "pdf/planes-mejora/{$anterior_anyo_academico}/{$fichero_paim}"
            );
        } else {
            echo Yii::t('cati', 'Plan anual de innovación y mejora (PAIM) del curso') . " {$anterior_anyo_academico}/{$anyo_academico}";
        }
        ?>
    </li>

    <li>
        <?php echo Html::a(
            Yii::t('cati', 'Acceso a los PAIM de todos los cursos'),
            "http://zaguan.unizar.es/search?ln={$language}&cc=plan-mejora-calidad&sc=1&as=1&m1=a&p1={$estudio->id_nk}&f1=codigo_titulacion&op1=a&m2=a&p2=&f2=&op2=a&m3=a&p3=&f3=&action_search=Search&dt=&d1d=&d1m=&d1y=&d2d=&d2m=&d2y=&sf=&so=a&rm=&rg=10&of=hb",
            ['target' => '_blank']
        ) . ' ' . Yii::t('cati', '(Repositorio institucional de documentos)'); ?> <span class="glyphicon glyphicon-link"></span>
    </li>

    <li><?php
        echo Html::a(
            Yii::t('cati', 'Memoria de verificación'),
            $url_memoria
        );
        ?>
    </li>

    <li><?php echo Html::a(
            Yii::t('cati', 'Informes de renovación de la acreditación'),
            "https://zaguan.unizar.es/search?ln={$language}&as=1&cc=informes-evaluacion-renovacion-acreditacion-titulaciones&m1=a&p1={$estudio->id_nk}&f1=codigo_titulacion&op1=a&action_search=Buscar",
            ['target' => '_blank']
        ); ?> <span class="glyphicon glyphicon-link"></span>
    </li>
</ul>


<!-- Comisiones -->
<h2><?php echo Yii::t('cati', 'Comisiones'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('cati', 'Agentes del sistema'),
        ['agente/lista', 'estudio_id' => $estudio->id]
    ); ?></li>
</ul>


<!-- Impresos -->
<h2><?php echo Yii::t('cati', 'Impresos'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('cati', 'Impreso de sugerencias, quejas y reclamaciones'),
        'https://unidadcalidad.unizar.es/sites/unidadcalidad.unizar.es/files/users/jsracio/impreso_sug_quejas_recl_doct.pdf'
    ); ?></li>
</ul>
