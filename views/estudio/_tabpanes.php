<?php
use yii\helpers\HtmlPurifier;
use yii\helpers\Html;
use yii\helpers\Url;

function mostrarPaginas($datos)
{
    foreach ($datos as $dato) {
        $texto = trim($dato->texto);
        if ($texto && $texto != "<br>" && $texto != "<p><br></p>" && $texto != "<p><br /></p>" && $texto != "<br><p></p>") {
            echo '<h2>' . Yii::t('db', $dato->seccion->titulo) . "</h2>\n";
            echo '<div>' . HtmlPurifier::process(
                $dato->texto,
                [
                    'Attr.ForbiddenClasses' => ['Apple-interchange-newline', 'Apple-converted-space',
                        'Apple-paste-as-quotation', 'Apple-style-span', 'Apple-tab-span',
                        'MsoChpDefault', 'MsoListParagraphCxSpFirst', 'MsoListParagraphCxSpLast',
                        'MsoListParagraphCxSpMiddle', 'MsoNormal', 'MsoNormalTable', 'MsoPapDefault', 'western', ],
                    'CSS.ForbiddenProperties' => ['border', 'border-bottom', 'border-left', 'border-right',
                        'border-top', 'font', 'font-family', 'font-size', 'font-weight', 'height', 'line-height',
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
}

$ids = [null, 'inicio', 'acceso', 'perfiles', 'queaprende', 'planes', 'apoyo', 'profesorado', 'calidad', 'resultados'];
?>

<div class="tab-content">
    <div class="tab-pane active" id="inicio">
        <?php
        if (isset($paginas[1])) {
            mostrarPaginas($paginas[1]);
        }
        ?>
    </div>

    <div class="tab-pane" id="acceso">
        <?php
        if (isset($paginas[2])) {
            mostrarPaginas($paginas[2]);
        }
        ?>
        <ul class="listado">
            <li><a href="https://academico.unizar.es/grado-y-master/legislacion/legislacion" target="_blank">
                <?php echo Yii::t('cati', 'Normativa académica/Legislación'); ?>
                </a> <span class="glyphicon glyphicon-link"></span>
            </li>
        </ul>
    </div>

    <?php
    foreach (range(3, 6) as $i) {
        printf("<div class='tab-pane' id='%s'>\n", $ids[$i]);
        if (isset($paginas[$i])) {
            mostrarPaginas($paginas[$i]);
        }
        echo "</div>\n";
    }
    ?>

    <div class="tab-pane" id="profesorado">
        <?php
        /* Grado/Master: Editar la información general -> Profesorado -> El profesorado de esta titulación (sección 71).
         * Doctorado: Líneas de investigación (sección 761), Equipos de investigación (sección 762).
         */
        if (isset($paginas[7])) {
            mostrarPaginas($paginas[7]);
        }
        ?>

        <ul class="listado">
            <!-- En los estudios nuevos `$anyo_profesorado` es null. -->
            <?php if ($anyo_profesorado) { ?>
                <li><?php echo Html::a(
                    Yii::t('cati', 'Estructura del profesorado'),
                    [
                        'informe/estructura-profesorado',
                        'estudio_id_nk' => $estudio->id_nk,
                        'anyo' => $anyo_profesorado,
                    ]
                ); ?></li>
            <?php } ?>
            <li><?php echo Html::a(
                Yii::t('cati', 'Evolución del profesorado'),
                ['informe/evolucion-profesorado', 'estudio_id_nk' => $estudio->id_nk]
            ); ?></li>
            <li><?php echo Html::a(
                Yii::t('cati', 'Perfil del Personal Docente e Investigador'),
                'https://janovas.unizar.es/sideral/CV/busqueda?lang=' . Yii::$app->language
            ); ?></li>
        </ul>
    </div>

    <div class='tab-pane' id='calidad'>
        <?php echo $this->render('_calidad', [
            'anyo_academico' => $anyo_academico,
            'estudio' => $estudio,
            'planes' => $planes,
            'version_informe' => $version_informe,
            'version_plan' => $version_plan,
        ]); ?>
    </div>

    <div class='tab-pane' id='encuestas'>
        <?php echo $this->render('_encuestas', [
            'estudio' => $estudio,
            'planes' => $planes,
        ]); ?>
    </div>
</div> <!-- tab-content -->
