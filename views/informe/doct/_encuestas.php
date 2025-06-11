<?php
/**
 * Fragmento de vista con la tabla de encuestas (Satisfacción y egreso).
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use app\models\Centro;

$titulo = (isset($apartado) ? "Tabla {$apartado}: " : '' ) . $titulo;
foreach($encuestas as $centro_id => $datos_del_centro) {
    $centro = Centro::findOne($centro_id);
    $anyos = array_shift($datos_del_centro);
?>

    <div class='table-responsive'>
        <table id="tabla_<?= $centro_id ?>" class="table table-striped table-hover cabecera-azul compact" style="width: 100%;">
            <caption>
                <div style='text-align: center; color: #777;'>
                    <p style='font-size: 140%;'><?= $titulo ?></p>
                    <!-- p>
                        <b>Estudio:</b> <?= $estudio->nombre ?><br>
                        <?php if ($centro) { ?>
                        <b>Centro:</b> <?= $centro->nombre ?><br>
                        <?php } ?>
                    </p -->
                </div>
            </caption>

            <thead>
                <tr>
                    <th scope='col' rowspan='2' style='vertical-align: middle;'>Encuesta</th>
                    <?php
                    foreach ($anyos as $pos => $anyo) {
                        echo "<th scope='col' colspan=2 style='text-align: center;'>{$anyo}</th>\n";
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    $cabecera2 = array_shift($datos_del_centro);
                    foreach ($cabecera2 as $pos => $cab) {
                        echo "<th scope='col' style='text-align: center;'>{$cab}</th>\n";
                    }
                    ?>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($datos_del_centro as $registro) {
                    echo "<tr>\n";
                    foreach ($registro as $pos => $dato) {
                        if ($pos === array_key_first($registro)) {
                            echo "<td>" . $dato ."</td>\n";
                        } else {
                            if ($dato) {
                                echo '<td style="text-align: right;">' . number_format($dato, 2) . "</td>\n";
                            } else {
                                echo "<td style='text-align: right;'>—</td>\n";
                            }
                        }
                    }
                    echo "</tr>\n";
                } ?>
            </tbody>
        </table>
    </div><br>
    <?php
}
