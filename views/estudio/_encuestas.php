<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Calendario;

// El 30 de octubre se publican las primeras encuestas del anterior año académico
$anyo_encuestas = Calendario::getAnyoAcademico() - 1;
$anyo_encuestas_anterior = $anyo_encuestas - 1;

// Al final de enero se publican los resultados del curso actual
// (oferta/nuevo ingreso/matrícula, créditos reconocidos...)
$anyo_resultados = date('m') < 2 ? date('Y') - 2 : date('Y') - 1;

/*
if ($estudio->id_nk === 659) {
    $planes = array_filter($planes, function ($p) {
        return in_array($p->id_nk, [415, 365, 366]);  // , 585, 586]);
    });
}
*/
?>

<!-- Encuestas -->
<h2><?php echo Yii::t('cati', 'Informes de encuestas'); ?></h2>

<?php
$urlbase = Url::base() . '/pdf/encuestas/' . $anyo_encuestas;
$urlbase_anterior = Url::base() . '/pdf/encuestas/' . $anyo_encuestas_anterior;
$pdfdir = Yii::getAlias('@webroot') . '/pdf/encuestas/' . $anyo_encuestas;
$pdfdir_anterior = Yii::getAlias('@webroot') . '/pdf/encuestas/' . $anyo_encuestas_anterior;
foreach ($planes as $plan) {
    // Enseñanza
    if (file_exists("$pdfdir/ensenanza/{$plan->centro_id}/{$plan->id_nk}_InformeEnsenanzaTitulacion.pdf")) {
        $ensenanza_url = "$urlbase/ensenanza/{$plan->centro_id}/{$plan->id_nk}_InformeEnsenanzaTitulacion.pdf";
        $anyo_ensenanza = $anyo_encuestas;
    } elseif (file_exists("$pdfdir_anterior/ensenanza/{$plan->centro_id}/{$plan->id_nk}_InformeEnsenanzaTitulacion.pdf")) {
        $ensenanza_url = "$urlbase_anterior/ensenanza/{$plan->centro_id}/{$plan->id_nk}_InformeEnsenanzaTitulacion.pdf";
        $anyo_ensenanza = $anyo_encuestas_anterior;
    } else {
        $ensenanza_url = null;
    }

    // Prácticas
    if (file_exists("$pdfdir/practicas/{$plan->centro_id}/{$plan->id_nk}_InformePracticasTitulacion.pdf")) {
        $practicas_url = "$urlbase/practicas/{$plan->centro_id}/{$plan->id_nk}_InformePracticasTitulacion.pdf";
        $anyo_practicas = $anyo_encuestas;
    } elseif (file_exists("$pdfdir_anterior/practicas/{$plan->centro_id}/{$plan->id_nk}_InformePracticasTitulacion.pdf")) {
        $practicas_url = "$urlbase_anterior/practicas/{$plan->centro_id}/{$plan->id_nk}_InformePracticasTitulacion.pdf";
        $anyo_practicas = $anyo_encuestas_anterior;
    } else {
        $practicas_url = null;
    }

    // Prácticas clínicas  -- Sólo en titulaciones de Ciencias de la Salud
    if (file_exists("$pdfdir/clinicas/{$plan->id_nk}/{$plan->id_nk}_InformePracClinicaTitulacion.pdf")) {
        $clinicas_url = "$urlbase/clinicas/{$plan->id_nk}/{$plan->id_nk}_InformePracClinicaTitulacion.pdf";
        $anyo_clinicas = $anyo_encuestas;
    } elseif (file_exists("$pdfdir_anterior/clinicas/{$plan->id_nk}/{$plan->id_nk}_InformePracClinicaTitulacion.pdf")) {
        $clinicas_url = "$urlbase_anterior/clinicas/{$plan->id_nk}/{$plan->id_nk}_InformePracClinicaTitulacion.pdf";
        $anyo_clinicas = $anyo_encuestas_anterior;
    } else {
        $clinicas_url = null;
    }

    // Satisfacción PAS - La satisfacción del PAS es por centro, no por plan.
    // En Teruel están agrupados en un centro ficticio con código 300.
    $cod_centro = $plan->centro_id;
    if ($plan->centro->municipio == 'Teruel') {
        $cod_centro = 300;
    }
    if (file_exists("{$pdfdir}/satisfaccionPAS/{$cod_centro}/{$cod_centro}_InformeSatisfaccionPAS.pdf")) {
        $pas_url = "{$urlbase}/satisfaccionPAS/{$cod_centro}/{$cod_centro}_InformeSatisfaccionPAS.pdf";
        $anyo_pas = $anyo_encuestas;
    } elseif (file_exists("$pdfdir_anterior/satisfaccionPAS/{$cod_centro}/{$cod_centro}_InformeSatisfaccionPAS.pdf")) {
        $pas_url = "$urlbase_anterior/satisfaccionPAS/{$cod_centro}/{$cod_centro}_InformeSatisfaccionPAS.pdf";
        $anyo_pas = $anyo_encuestas_anterior;
    } else {
        $pas_url = null;
    }

    // Satisfacción PDI
    if (file_exists("$pdfdir/satisfaccionPDI/{$plan->centro_id}/{$plan->id_nk}_InformeSatisfaccionPDI.pdf")) {
        $pdi_url = "$urlbase/satisfaccionPDI/{$plan->centro_id}/{$plan->id_nk}_InformeSatisfaccionPDI.pdf";
        $anyo_pdi = $anyo_encuestas;
    } elseif (file_exists("$pdfdir_anterior/satisfaccionPDI/{$plan->centro_id}/{$plan->id_nk}_InformeSatisfaccionPDI.pdf")) {
        $pdi_url = "$urlbase_anterior/satisfaccionPDI/{$plan->centro_id}/{$plan->id_nk}_InformeSatisfaccionPDI.pdf";
        $anyo_pdi = $anyo_encuestas_anterior;
    } else {
        $pdi_url = null;
    }

    // Movilidad
    if (file_exists("$pdfdir/movilidad/{$plan->centro_id}/{$plan->id_nk}_InformeMovilidad.pdf")) {
        $movilidad_url = "$urlbase/movilidad/{$plan->centro_id}/{$plan->id_nk}_InformeMovilidad.pdf";
        $anyo_movilidad = $anyo_encuestas;
    } elseif (file_exists("$pdfdir_anterior/movilidad/{$plan->centro_id}/{$plan->id_nk}_InformeMovilidad.pdf")) {
        $movilidad_url = "$urlbase_anterior/movilidad/{$plan->centro_id}/{$plan->id_nk}_InformeMovilidad.pdf";
        $anyo_movilidad = $anyo_encuestas_anterior;
    } else {
        $movilidad_url = null;
    }

    // Satisfacción Titulación
    if (file_exists("$pdfdir/satisfaccionTitulacion/{$plan->centro_id}/{$plan->id_nk}_InformeSatisfaccionTitulacionEstudiantes.pdf")) {
        $est_url = "$urlbase/satisfaccionTitulacion/{$plan->centro_id}/{$plan->id_nk}_InformeSatisfaccionTitulacionEstudiantes.pdf";
        $anyo_est = $anyo_encuestas;
    } elseif (file_exists("$pdfdir_anterior/satisfaccionTitulacion/{$plan->centro_id}/{$plan->id_nk}_InformeSatisfaccionTitulacionEstudiantes.pdf")) {
        $est_url = "$urlbase_anterior/satisfaccionTitulacion/{$plan->centro_id}/{$plan->id_nk}_InformeSatisfaccionTitulacionEstudiantes.pdf";
        $anyo_est = $anyo_encuestas_anterior;
    } else {
        $est_url = null;
    }

    // TFG/TFM
    if (file_exists("$pdfdir/TfgTfm/{$plan->centro_id}/{$plan->id_nk}_InformeTfgTfm.pdf")) {
        $tfg_url = "$urlbase/TfgTfm/{$plan->centro_id}/{$plan->id_nk}_InformeTfgTfm.pdf";
        $anyo_tfg = $anyo_encuestas;
    } elseif (file_exists("$pdfdir_anterior/TfgTfm/{$plan->centro_id}/{$plan->id_nk}_InformeTfgTfm.pdf")) {
        $tfg_url = "$urlbase_anterior/TfgTfm/{$plan->centro_id}/{$plan->id_nk}_InformeTfgTfm.pdf";
        $anyo_tfg = $anyo_encuestas_anterior;
    } else {
        $tfg_url = null;
    }

    printf('<h3>%s (%s %d)</h3>', Html::encode($plan->centro->nombre), Yii::t('cati', 'plan'), $plan->id_nk); ?>

    <ul class="listado">
    <?php
    if ($ensenanza_url) {
        $nombre = Yii::t('cati', 'Valoración de la docencia (bloque enseñanza)');
        if ($anyo_ensenanza < 2022) $nombre = Yii::t('cati', 'Evaluación de la enseñanza');
        printf(
            "<li>%s %d/%d</li>\n",
            Html::a($nombre, $ensenanza_url),
            $anyo_ensenanza,
            $anyo_ensenanza + 1
        );
    }

    if ($movilidad_url) {
        printf(
            "<li>%s %d/%d</li>\n",
            Html::a(Yii::t('cati', 'Programas de movilidad: Erasmus'), $movilidad_url),
            $anyo_movilidad,
            $anyo_movilidad + 1
        );
    }

    if ($practicas_url) {
        printf(
            "<li>%s %d/%d</li>\n",
            Html::a(Yii::t('cati', 'Evaluación de las prácticas externas por los estudiantes'), $practicas_url),
            $anyo_practicas,
            $anyo_practicas + 1
        );
    }

    if ($clinicas_url) {
        printf(
            "<li>%s %d/%d</li>\n",
            Html::a(Yii::t('cati', 'Evaluación de las prácticas clínicas por los alumnos'), $clinicas_url),
            $anyo_clinicas,
            $anyo_clinicas + 1
        );
    }

    if ($pas_url) {
        printf(
            "<li>%s %d/%d</li>\n",
            Html::a(Yii::t('cati', 'Satisfacción del PAS con el centro'), $pas_url),
            $anyo_pas,
            $anyo_pas + 1
        );
    }

    if ($pdi_url) {
        printf(
            "<li>%s %d/%d</li>\n",
            Html::a(Yii::t('cati', 'Satisfacción del PDI con la titulación'), $pdi_url),
            $anyo_pdi,
            $anyo_pdi + 1
        );
    }

    if ($est_url) {
        printf(
            "<li>%s %d/%d</li>\n",
            Html::a(Yii::t('cati', 'Satisfacción de los estudiantes con la titulación'), $est_url),
            $anyo_est,
            $anyo_est + 1
        );
    }

    if ($tfg_url) {
        printf(
            "<li>%s %d/%d</li>\n",
            Html::a(Yii::t('cati', 'Satisfacción con el Trabajo de Fin de Grado o Máster'), $tfg_url),
            $anyo_tfg,
            $anyo_tfg + 1
        );
    } ?>
    </ul>
    <?php
} //endforeach planes
?>

<h3><?php echo Yii::t('cati', 'Encuestas de años anteriores'); ?></h3>

<ul class="listado">
    <li><?php echo Html::a(
        Yii::t('cati', 'Histórico de informes de encuestas'),
        ['site/ver-encuestas']
    ); ?></li>
    <li><?php echo Html::a(
        Yii::t('cati', 'Satisfacción y egreso'),
        ['informe/encuestas', 'anyo' => $anyo_resultados, 'estudio_id_nk' => $estudio->id_nk]
    ); ?></li>
</ul>

<!-- --------------------------- Resultados académicos -------------------------- -->
<h2><?php echo Yii::t('cati', 'Resultados académicos'); ?></h2>

<ul class="listado">
    <li><?php printf("%s %d/%d\n", Yii::t('cati', 'Resultados académicos'), $anyo_resultados, $anyo_resultados + 1); ?>
        (<a href="<?php echo Yii::getAlias('@web') ?>/pdf/definiciones_web_estudios_curso_v11.pdf"><span class="icon-info-with-circle"></span></a>)
        <ul>
        <?php
        // Durante enero están publicados los datos del curso anterior.
        // El 1 de febrero se publican los datos del curso actual de
        // * Estudio previo de los estudiantes de nuevo ingreso
        // * Nota media de admisión
        // * Plazas de nuevo ingreso ofertadas
        // El resto de los datos se publican en octubre, tras la
        // convocatoria de septiembre.
        // Para actualizar los resultados deben ir a:
        // Gestión -> Resultados -> Actualizar datos académicos de Grado y Máster
        if (date('m') < 2 or date('m') >= 10) {
            /*
            echo '<li>' . Html::a(
                Yii::t('cati', 'Estudiantes en planes de movilidad'),
                ['informe/planes-movilidad', 'estudio_id' => $estudio->id, 'anyo' => $anyo_resultados]
            ) . "</li>\n";
            */
            echo '<li>' . Html::a(
                Yii::t('cati', 'Análisis de los indicadores del título'),
                ['informe/indicadores', 'estudio_id' => $estudio->id, 'anyo' => $anyo_resultados]
            ) . "</li>\n";
            echo '<li>' . Html::a(
                Yii::t('cati', 'Distribución de calificaciones'),
                ['informe/calificaciones', 'estudio_id' => $estudio->id, 'anyo' => $anyo_resultados]
            ) . "</li>\n";
        }
        echo '<li>' . Html::a(
            Yii::t('cati', 'Estudio previo de los estudiantes de nuevo ingreso'),
            ['informe/estudio-previo', 'estudio_id' => $estudio->id, 'anyo' => $anyo_resultados]
        ) . "</li>\n";
        echo '<li>' . Html::a(
            Yii::t('cati', 'Perfil de ingreso de los estudiantes: procedencia (residencia familiar)'),
            ['informe/procedencia', 'estudio_id' => $estudio->id, 'anyo' => $anyo_resultados]
        ) . "</li>\n";
        echo '<li>' . Html::a(
            Yii::t('cati', 'Perfil de ingreso de los estudiantes: género'),
            ['informe/genero', 'estudio_id' => $estudio->id, 'anyo' => $anyo_resultados]
        ) . "</li>\n";
        echo '<li>' . Html::a(
            Yii::t('cati', 'Perfil de ingreso de los estudiantes: edad'),
            ['informe/edad', 'estudio_id' => $estudio->id, 'anyo' => $anyo_resultados]
        ) . "</li>\n";
        echo '<li>' . Html::a(
            Yii::t('cati', 'Nota media de admisión'),
            ['informe/nota-media', 'estudio_id' => $estudio->id, 'anyo' => $anyo_resultados]
        ) . "</li>\n";
        echo '<li>' . Html::a(
            Yii::t('cati', 'PAS de apoyo a la docencia'),
            ['informe/pas', 'estudio_id' => $estudio->id]
        ) . "</li>\n";
        echo '<li>' . Html::a(
            Yii::t('cati', 'Innovación docente'),
            ['informe/innovacion-docente', 'estudio_id_nk' => $estudio->id_nk, 'anyo' => $anyo_resultados]
        ) . "</li>\n";
        echo '<li>' . Html::a(
            Yii::t('cati', 'Estudiantes en planes de movilidad'),
            ['informe/movilidad', 'estudio_id' => $estudio->id]
        ) . "</li>\n";
        echo '<li>' . Html::a(
            Yii::t('cati', 'Plazas de nuevo ingreso ofertadas'),
            ['informe/plazas-nuevo-ingreso', 'estudio_id' => $estudio->id, 'anyo' => $anyo_resultados]
        ) . "</li>\n";
        ?>
        </ul>
    </li>

    <li><?php echo Html::a(
        Yii::t('cati', 'Resultados académicos de años anteriores'),
        ['informe/resultados-academicos', 'estudio_id' => $estudio->id]
    ); ?></li>

    <li><?php
        echo Html::a(
            Yii::t('cati', 'Resultados académicos globales'),
            ['informe/globales', 'estudio_id' => $estudio->id]
        ) . "\n"; ?>
        <ul>
            <li><?php echo Html::a(
                Yii::t('cati', 'Oferta / Nuevo ingreso / Matrícula'),
                ['informe/globales-nuevo-ingreso', 'estudio_id' => $estudio->id]
            ); ?></li>

            <li><?php echo Html::a(
                Yii::t('cati', 'Créditos reconocidos'),
                ['informe/globales-creditos', 'estudio_id' => $estudio->id]
            ); ?></li>

            <li><?php echo Html::a(
                Yii::t('cati', 'Cursos de adaptación al grado'),
                ['informe/globales-adaptacion', 'estudio_id' => $estudio->id]
            ); ?></li>

            <li><?php echo Html::a(
                Yii::t('cati', 'Duración media graduados'),
                ['informe/globales-duracion', 'estudio_id' => $estudio->id]
            ); ?></li>

            <li><?php echo Html::a(
                Yii::t('cati', 'Tasas de éxito/rendimiento/eficiencia'),
                ['informe/globales-exito', 'estudio_id' => $estudio->id]
            ); ?></li>

            <li><?php echo Html::a(
                Yii::t('cati', 'Tasas de abandono/graduación'),
                ['informe/globales-abandono', 'estudio_id' => $estudio->id]
            ); ?></li>
        </ul>
    </li>
</ul>
