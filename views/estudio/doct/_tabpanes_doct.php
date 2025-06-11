<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

function mostrarPaginas($infos, $datos)
{
    foreach ($infos as $info) {
        if (761 == $info->seccion->id) {  // Líneas de investigación de doctorado, ya mostradas
            continue;
        }

        echo '<h2>' . Yii::t('db', $info->seccion->titulo) . "</h2>\n";
        if (732 == $info->seccion->id && $datos) {  // Acceso, admisión y matrícula => Información específica del programa
            echo "<p><b>Oferta de plazas: </b>" . $datos->plazas_ofertadas . "</p><br>\n";
        }
        echo '<div>' . HtmlPurifier::process(
            $info->texto,
            [
                'Attr.ForbiddenClasses' => ['Apple-interchange-newline', 'Apple-converted-space',
                    'Apple-paste-as-quotation', 'Apple-style-span', 'Apple-tab-span',
                    'MsoChpDefault', 'MsoListParagraphCxSpFirst', 'MsoListParagraphCxSpLast',
                    'MsoListParagraphCxSpMiddle', 'MsoNormal', 'MsoNormalTable', 'MsoPapDefault', 'western', ],
                'CSS.ForbiddenProperties' => ['border', 'border-bottom', 'border-left', 'border-right', 'border-top',
                    'font', 'font-family', 'font-size', 'font-weight', 'height', 'line-height',
                    'margin', 'margin-bottom', 'margin-left', 'margin-right', 'margin-top',
                    'padding', 'padding-bottom', 'padding-left', 'padding-right', 'padding-top',
                    'text-autospace', 'text-indent', 'width', ],
                'HTML.ForbiddenAttributes' => ['align', 'background', 'bgcolor', 'border',
                    'cellpadding', 'cellspacing', 'height', 'hspace', 'noshade', 'nowrap',
                    'rules', 'size', 'valign', 'vspace', 'width', ],
                'HTML.ForbiddenElements' => ['font'],
                // 'data' permite incrustar imágenes en base64
                'URI.AllowedSchemes' => ['data' => true, 'http' => true, 'https' => true, 'mailto' => true],
            ]
        ) . "</div>\n";
        echo "<br style='clear: both'>\n";
    }
}

$ids = [null, 'info', 'competencias', 'admision', 'organizacion', 'actividades', 'rrhh', 'rrmm'];
?>

<div class="tab-content">
    <div class="tab-pane active" id="info">
        <?php
        if (isset($paginas[1])) {
            mostrarPaginas($paginas[1], $datos);
        }
        ?>
        <ul class="listado">
            <li><?php echo Html::a(
                Yii::t('cati', 'Comisiones'),
                ['agente/lista', 'estudio_id' => $estudio->id]
            ); ?></li>
        </ul>
    </div>

    <?php
    foreach (range(2, count($ids) - 1) as $i) {
        printf("<div class='tab-pane' id='%s'>\n", $ids[$i]);
        if ('rrhh' == $ids[$i]) {
            // Mostramos las líneas de investigación de doctorado, obtenidas de Sigm@
            echo $this->render('_lineas', ['lineas' => $lineas]);
            // Mostramos los equipos de investigación, obtenidos de People
            echo $this->render(
                '_equipos',
                ['nombres_equipos' => $nombres_equipos, 'miembros_equipos' => $miembros_equipos]
            );
        } elseif (isset($paginas[$i])) {
            mostrarPaginas($paginas[$i], $datos);
        }
        echo "</div>\n";
    }
    ?>

    <div class='tab-pane' id='calidad'>
        <?php echo $this->render(
            '_calidad_doct', [
                'anyo_academico' => $anyo_academico,
                'estudio' => $estudio,
                'version_informe' => $version_informe,
                'version_plan' => $version_plan,
            ]
        ); ?>
    </div>

    <div class='tab-pane' id='encuestas'>
        <?php echo $this->render(
            '_encuestas_doct', [
                'estudio' => $estudio,
            ]
        ); ?>
    </div>
</div> <!-- tab-content -->
