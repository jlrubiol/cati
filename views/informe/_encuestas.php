<?php
use app\models\Plan;

$subnum_tabla = 0;
foreach($encuestas as $plan_id_nk => $datos_del_plan) {
    $plan = Plan::findOne(['id_nk' => $plan_id_nk, 'anyo_academico' => $estudio->anyo_academico]);
    $anyos = array_shift($datos_del_plan);
    $subnum_tabla++;
    $titulo = (isset($apartado) ? "Tabla {$apartado}.{$subnum_tabla}: " : '' ) . 'Satisfacción y egreso';
?>

    <div class='table-responsive'>
        <table id="tabla_<?= $plan_id_nk ?>" class="table table-striped table-hover cabecera-azul compact" style="width: 100%;">
            <caption>
                <div style='text-align: center; color: #777;'>
                    <p style='font-size: 140%;'><?= $titulo ?></p>
                    <p>
                        <b>Estudio:</b> <?= $estudio->nombre ?><br>
                        <b>Centro:</b> <?= $plan->centro->nombre ?><br>
                        <b>Plan:</b> <?= $plan->id_nk ?>
                    </p>
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
                    $cabecera2 = array_shift($datos_del_plan);
                    foreach ($cabecera2 as $pos => $cab) {
                        echo "<th scope='col' style='text-align: center;'>{$cab}</th>\n";
                    }
                    ?>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($datos_del_plan as $registro) {
                    echo "<tr>\n";
                    foreach ($registro as $pos => $dato) {
                        if ($pos === array_key_first($registro)) {
                            echo "<td>" . $dato ."</td>\n";
                        } else {
                            if ($dato) {
                                echo '<td style="text-align: right;">' . number_format($dato, 2) . "</td>\n";
                            } else {
                                echo '<td style="text-align: right;">—</td>' . "\n";
                            }
                        }
                    }
                    echo "</tr>\n";
                } ?>
            </tbody>
        </table>
        <p>En la encuesta de valoración de la docencia:
            <ul>
                <li>El dato de la tasa se refiere a Encuesta de valoración de la docencia (bloque enseñanza)</li>
                <li>El dato de la Media se refiere a Encuesta de valoración de la docencia (bloque profesorado)</li>
            </ul>
        </p>
    </div><br>
    <?php
}
