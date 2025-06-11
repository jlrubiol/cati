<?php

use \Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Estudio;
use app\models\InformePublicado;
use app\models\PlanPublicado;

$language = Yii::$app->language;
$anterior_anyo_academico = $anyo_academico - 1;

$estudio_anterior = Estudio::find()->where(['anyo_academico' => $anterior_anyo_academico, 'id_nk' => $estudio->id_nk])->one();
$dir_informes = Yii::getAlias('@webroot') . "/pdf/informes/{$anterior_anyo_academico}";
$dir_planes = Yii::getAlias('@webroot') . "/pdf/planes-mejora/{$anterior_anyo_academico}";
$fichero_informe = "informe-{$language}-{$estudio->id_nk}-v{$version_informe}.pdf";
$fichero_plan = "plan-{$language}-{$estudio->id_nk}-v{$version_plan}.pdf";
$hay_informe = file_exists("{$dir_informes}/{$fichero_informe}");
$hay_plan = file_exists("{$dir_planes}/{$fichero_plan}");
?>

<!-- Impresos -->
<h2><?php echo Yii::t('cati', 'Impresos'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('cati', 'Impreso de sugerencias, quejas y reclamaciones'),
        'https://unidadcalidad.unizar.es/sites/unidadcalidad.unizar.es/files/users/jsracio/impreso_sug_quejas_recl.pdf'
); ?></li>
</ul>


<!-- Normativa -->
<h2><?php echo Yii::t('cati', 'Normativa'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(Yii::t('cati', 'Cómo se asegura la calidad'), ['pagina/ver', 'id' => 1]); ?></li>
    <li><?php echo Html::a(
        Yii::t(
            'cati',
            'Reglamento de la Organización y gestión de la calidad de los estudios de grado y máster universitario'
        ),
        ['pagina/ver', 'id' => 2]
); ?></li>
    <li><?php echo Html::a(
        Yii::t('cati', 'Procedimientos del sistema interno de gestión de la calidad'),
        ['pagina/ver', 'id' => 7]
); ?></li>
</ul>

<?php
// No mostrar la parte de Documentos (Informes, Memoria…) de los títulos conjuntos.
if (!in_array($estudio->id_nk, Estudio::FALSOS_ESTUDIO_IDS)) {
    ?>
    <!-- Documentos -->
    <h2><?php echo Yii::t('cati', 'Documentos'); ?></h2>

    <ul class="listado">
        <li>
        <?php
        if (InformePublicado::MAX_VERSION_INFORME === $version_informe and $hay_informe) {
            echo Html::a(
                Yii::t('cati', 'Informe de Evaluación de la Calidad')
                    . ' ' . $anterior_anyo_academico . '/' . $anyo_academico,
                Url::base() . "/pdf/informes/{$anterior_anyo_academico}/{$fichero_informe}"
            );
        } else {
            printf(
                Yii::t('cati', 'Informe de Evaluación de la Calidad %d/%d'),
                $anterior_anyo_academico,
                $anyo_academico
            );
        }

        if ($version_informe < InformePublicado::MAX_VERSION_INFORME
            and Yii::$app->user->can('editarInforme', ['estudio' => $estudio])
            and $estudio_anterior
        ) {
            echo ' &nbsp; ' . Html::a(
                '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('cati', 'Ver/Editar borrador'),
                ['informe/ver', 'estudio_id' => $estudio_anterior->id, 'anyo' => $anterior_anyo_academico],
                [
                    'id' => 'ver-informe',
                    'class' => 'btn btn-info btn-xs',
                ]
            );
        } ?>
        </li>

        <li><?php echo Html::a(
            Yii::t('cati', 'Informe de evaluación de años anteriores'),
            "http://zaguan.unizar.es/search?ln={$language}&cc=informe-autoevaluacion-calidad&sc=1&as=1&m1=a&p1={$estudio->id_nk}&f1=codigo_titulacion&op1=a&m2=a&p2=&f2=&op2=a&m3=a&p3=&f3=&action_search=Search&dt=&d1d=&d1m=&d1y=&d2d=&d2m=&d2y=&sf=&so=a&rm=&rg=10&of=hb",
            ['target' => '_blank']
            ); ?> <span class="glyphicon glyphicon-link"></span></li>

        <li>
        <?php
        if (PlanPublicado::MAX_VERSION_PLAN === $version_plan and $hay_plan) {
            echo Html::a(
                Yii::t('cati', 'Plan anual de innovación y mejora') . ' ' . $anterior_anyo_academico . '/' . $anyo_academico,
                Url::base() . "/pdf/planes-mejora/{$anterior_anyo_academico}/{$fichero_plan}"
            );
        } else {
            echo Yii::t('cati', 'Plan anual de innovación y mejora') . ' ' . $anterior_anyo_academico . '/' . $anyo_academico;
        }

        if ($version_plan < PlanPublicado::MAX_VERSION_PLAN and \Yii::$app->user->can('editarPlan', ['estudio' => $estudio]) and $estudio_anterior) {
            echo ' &nbsp; ' . Html::a(
                '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('cati', 'Ver/editar borrador'),
                ['plan-mejora/ver', 'estudio_id' => $estudio_anterior->id, 'anyo' => $anterior_anyo_academico],
                [
                    'id' => 'ver-plan',
                    'class' => 'btn btn-info btn-xs',
                ]
            );
        } ?>
        </li>

        <li><?php echo Html::a(
            Yii::t('cati', 'Plan anual de innovación y mejora de años anteriores'),
            "http://zaguan.unizar.es/search?ln={$language}&cc=plan-mejora-calidad&sc=1&as=1&m1=a&p1={$estudio->id_nk}&f1=codigo_titulacion&op1=a&m2=a&p2=&f2=&op2=a&m3=a&p3=&f3=&action_search=Search&dt=&d1d=&d1m=&d1y=&d2d=&d2m=&d2y=&sf=&so=a&rm=&rg=10&of=hb",
            ['target' => '_blank']
            ); ?> <span class="glyphicon glyphicon-link"></span></li>

        <li><?php echo Html::a(
            Yii::t('cati', 'Informes y planes de mejora de todas las titulaciones'),
            ['site/acpua']
        ); ?></li>

        <li><?php echo Html::a(
            Yii::t('cati', 'Memoria de verificación'),
            $estudio->getEnlaceMemoriaVerificacion()
        ); ?></li>

        <li><?php echo Html::a(
            Yii::t('cati', 'Informes de renovación de la acreditación'),
            "https://zaguan.unizar.es/search?ln={$language}&as=1&cc=informes-evaluacion-renovacion-acreditacion-titulaciones&m1=a&p1={$estudio->id_nk}&f1=codigo_titulacion&op1=a&action_search=Buscar",
            ['target' => '_blank']
        ); ?> <span class="glyphicon glyphicon-link"></span></li>

        <?php
            foreach ($planes as $plan) {
                $centro = $plan->centro;
                if ($centro->acreditacion_url) {
                    echo Html::a(
                        '<li>' . Yii::t('cati', 'Acreditación institucional') . ' - ' .  Html::encode($plan->centro->nombre),
                        $centro->acreditacion_url
                    ) . ' <span class="glyphicon glyphicon-link"></span></li>';
                }
            }
        ?>
    </ul>
    <?php
}
?>


<!-- Comisiones -->
<h2><?php echo Yii::t('cati', 'Comisiones'); ?></h2>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('cati', 'Agentes del sistema'),
        ['agente/lista', 'estudio_id' => $estudio->id]
); ?></li>
</ul>
