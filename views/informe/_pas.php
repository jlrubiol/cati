<?php
use app\models\Centro;

$subnum_tabla = 0;
foreach($evolucionesPas as $centro_id => $datos_del_centro) {
    $centro = Centro::findOne($centro_id);
    $cabeceras = array_shift($datos_del_centro);
    $subnum_tabla++;
    $titulo = (isset($apartado) ? "Tabla {$apartado}.{$num_tabla}.{$subnum_tabla}: " : '' ) . 'Evolución del PAS de apoyo a la docencia';
?>
    <div class='table-responsive'>
        <table id="tabla_<?= $centro_id ?>_pas" class="table table-striped table-hover cabecera-azul compact" style="width: 100%;">
            <caption>
                <div style='text-align: center; color: #777;'>
                    <p style='font-size: 140%;'><?= $titulo ?></p>
                    <p>
                        <b>Estudio:</b> <?= $estudio->nombre ?><br>
                        <b>Centro:</b> <?= $centro->nombre ?>
                    </p>
                </div>
            </caption>

            <thead>
                <tr>
                    <?php
                    foreach ($cabeceras as $pos => $cabecera) {
                        if ($pos <= 1) {
                            echo "<th scope='col'>" . $cabecera . "</th>\n";
                        } else {
                            echo "<th scope='col' style='text-align: right;'>" . $cabecera . "</th>\n";
                        }
                    }
                    ?>
                </tr>
            </thead>

            <tbody>
                <?php
                $un_registro = $datos_del_centro[0] ?? [];
                ksort($un_registro);
                $anyos = array_slice(array_keys($un_registro), 0, -2);
                $end = end($datos_del_centro);
                foreach ($datos_del_centro as $registro) {
                    if ($registro === $end) {
                        echo "<tfoot>\n";
                    }
                    echo "<tr>\n";
                    echo "<td>{$registro['ESPECIALIDAD']}</td>";
                    echo "<td>{$registro['TIPO_EMPLEADO']}</td>";

                    foreach ($anyos as $anyo) {
                        echo "<td style='text-align: right;'>{$registro[$anyo]}</td>\n";
                    }
                    echo "</tr>\n";
                    if ($registro === $end) {
                        echo "</tfoot>\n";
                    }
                } ?>
            </tbody>
        </table>
    </div><br />
    <?php
}