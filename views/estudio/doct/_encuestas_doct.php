<?php
use yii\helpers\Html;
use yii\helpers\Url;

// Anterior año académico
$anyo_academico = date('m') < 10 ? date('Y') - 1 : date('Y');
$anyo_academico_anterior = $anyo_academico - 1;
$anyo_academico_2anterior = $anyo_academico - 2;
$pdfdir = Yii::getAlias('@webroot') . "/pdf/encuestas/{$anyo_academico}";
$urlbase = Url::base() . "/pdf/encuestas/{$anyo_academico}";
$pdfdir_anterior = Yii::getAlias('@webroot') . "/pdf/encuestas/{$anyo_academico_anterior}";
$urlbase_anterior = Url::base() . "/pdf/encuestas/{$anyo_academico_anterior}";
$pdfdir_2anterior = Yii::getAlias('@webroot') . "/pdf/encuestas/{$anyo_academico_2anterior}";
$urlbase_2anterior = Url::base() . "/pdf/encuestas/{$anyo_academico_2anterior}";
?>

<h2><?php echo Yii::t('doct', 'Indicadores'); ?></h2>

<ul class='listado'>
    <li><?php echo Html::a(
        Yii::t('doct', 'Nuevo ingreso'),
        ['//informe/doct-nuevo-ingreso', 'estudio_id' => $estudio->id]
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('doct', 'Matrícula'),
        ['//informe/doct-matriculados', 'estudio_id' => $estudio->id]
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('doct', 'Resultados de la formación'),
        ['//informe/doct-resultados-formacion', 'estudio_id' => $estudio->id]
    ); ?></li>
</ul>

<h2><?php echo Yii::t('cati', 'Informes de encuestas'); ?></h2>

<?php
foreach ($estudio->plans as $plan) {
    $doctorandos_url = "{$urlbase}/doctorado/{$plan->id_nk}/{$plan->id_nk}_InformeDoctoradoEst.pdf";
    $directores_url = "{$urlbase}/doctorado//{$plan->id_nk}/{$plan->id_nk}_InformeDoctorado.pdf";
    $doctorandos_file = "{$pdfdir}/doctorado/{$plan->id_nk}/{$plan->id_nk}_InformeDoctoradoEst.pdf";
    $directores_file = "{$pdfdir}/doctorado/{$plan->id_nk}/{$plan->id_nk}_InformeDoctorado.pdf";

    $doctorandos_url_anterior = "{$urlbase_anterior}/doctorado/{$plan->id_nk}/{$plan->id_nk}_InformeDoctoradoEst.pdf";
    $directores_url_anterior = "{$urlbase_anterior}/doctorado/{$plan->id_nk}/{$plan->id_nk}_InformeDoctorado.pdf";
    $egresados_url_anterior = "{$urlbase_anterior}/doctoradoEgresados/{$plan->id_nk}/{$plan->id_nk}_InformeDoctoradoEgresados.pdf";
    $doctorandos_file_anterior = "{$pdfdir_anterior}/doctorado/{$plan->id_nk}/{$plan->id_nk}_InformeDoctoradoEst.pdf";
    $directores_file_anterior = "{$pdfdir_anterior}/doctorado/{$plan->id_nk}/{$plan->id_nk}_InformeDoctorado.pdf";
    $egresados_file_anterior = "{$pdfdir_anterior}/doctoradoEgresados/{$plan->id_nk}/{$plan->id_nk}_InformeDoctoradoEgresados.pdf";

    $egresados_url_2anterior = "{$urlbase_2anterior}/doctoradoEgresados/{$plan->id_nk}/{$plan->id_nk}_InformeDoctoradoEgresados.pdf";
    $egresados_file_2anterior = "{$pdfdir_2anterior}/doctoradoEgresados/{$plan->id_nk}/{$plan->id_nk}_InformeDoctoradoEgresados.pdf";

    echo "<ul class='listado'>";

    // Último año
    if (file_exists($doctorandos_file)) {
        echo '<li>' . Html::a(
            sprintf(
                '%s %d/%d',
                Yii::t('doct', 'Satisfacción de los estudiantes con el doctorado'),
                $anyo_academico,
                $anyo_academico + 1
            ),
            $doctorandos_url
        ) . "</li>\n";
    }

    if (file_exists($directores_file)) {
        echo '<li>' . Html::a(
            sprintf(
                '%s %d/%d',
                Yii::t('doct', 'Satisfacción de los directores/tutores con el doctorado'),
                $anyo_academico,
                $anyo_academico + 1
            ),
            $directores_url
        ) . "</li>\n";
    }

    // Penúltimo año
    if (file_exists($doctorandos_file_anterior)) {
        echo '<li>' . Html::a(
            sprintf(
                '%s %d/%d',
                Yii::t('doct', 'Satisfacción de los estudiantes con el doctorado'),
                $anyo_academico_anterior,
                $anyo_academico
            ),
            $doctorandos_url_anterior
        ) . "</li>\n";
    }

    if (file_exists($directores_file_anterior)) {
        echo '<li>' . Html::a(
            sprintf(
                '%s %d/%d',
                Yii::t('doct', 'Satisfacción de los directores/tutores con el doctorado'),
                $anyo_academico_anterior,
                $anyo_academico
            ),
            $directores_url_anterior
        ) . "</li>\n";
    }

    if (file_exists($egresados_file_anterior)) {
        echo '<li>' . Html::a(
            sprintf(
                '%s %d/%d',
                Yii::t('doct', 'Satisfacción e inserción laboral de egresados de la Escuela de Doctorado'),
                $anyo_academico_anterior,
                $anyo_academico
            ),
            $egresados_url_anterior
        ) . "</li>\n";
    }

    if (file_exists($egresados_file_2anterior)) {
        echo '<li>' . Html::a(
            sprintf(
                '%s %d/%d',
                Yii::t('doct', 'Satisfacción e inserción laboral de egresados de la Escuela de Doctorado'),
                $anyo_academico_2anterior,
                $anyo_academico_anterior
            ),
            $egresados_url_2anterior
        ) . "</li>\n";
    }
?>
</ul>

<h3><?php echo Yii::t('cati', 'Encuestas de años anteriores'); ?></h3>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('cati', 'Histórico de informes de encuestas'),
        ['site/ver-encuestas-doct']
    ); ?></li>

<?php
    echo '<li>' . Html::a(
        Yii::t('doct', 'Encuestas a egresados doctorado 2017/2018. Resumen ejecutivo'),
        '@web/pdf/encuestas/2017_egresados_doctorado.pdf'
    ) . "</li>\n";

    echo '<li>' . Html::a(
        Yii::t('doct', 'Encuestas a egresados doctorado 2016/2017. Resumen ejecutivo'),
        '@web/pdf/encuestas/2016_egresados_doctorado.pdf'
    ) . "</li>\n";

    /*
    echo '<li>' . Html::a(
        Yii::t('doct', 'Resultados de las encuestas a egresados')  . " {$anyo_academico_anterior}",
        "https://escueladoctorado.unizar.es/es/calidad-informes-{$anyo_academico_anterior}"
    ) . "</li>\n";
    */

    echo '</ul>';
}
