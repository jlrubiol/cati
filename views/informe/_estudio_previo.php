<?php

use yii\widgets\ListView;
use app\models\Centro;

// Desde ver/editar el informe se incluye este fragmento de vista independientemente del tipo del estudio.
// Si es de tipo máster, pasar a _estudio_previo_master.
if ($estudio->esMaster()) {
    echo $this->render('_estudio_previo_master', [
        'anyo' => $anyo,
        'apartado' => $apartado,
        'dpsEstudiosPrevios' => $dpsEstudiosPrevios,
        'estudio' => $estudio,
        'num_tabla' => $num_tabla,
    ]);

    return;
}

if ($estudiosPrevios) {
    $subnum_tabla = 0;
    foreach($estudiosPrevios as $centro_id => $datos) {
        $centro = Centro::findOne($centro_id);
        $anyos = array_shift($datos);
        $subnum_tabla++;
        $titulo = (isset($apartado) ? "Tabla {$apartado}.{$num_tabla}.{$subnum_tabla}: " : '' ) . 'Estudio previo de los estudiantes de nuevo ingreso';
    ?>

        <div class='table-responsive'>
            <table id="tabla_centro_<?= $centro_id ?>" class="table table-striped table-hover cabecera-azul compact" style="width: 100%;">
                <caption>
                    <div style='text-align: center; color: #777;'>
                        <p style='font-size: 140%;'><?= $titulo ?></p>
                        <!-- p style='font-size: 120%;'>Año académico: </p -->
                        <p>
                            <b>Estudio:</b> <?= $estudio-> nombre ?><br>
                            <?php if ($centro) { ?>
                            <b>Centro:</b> <?= $centro->nombre ?><br>
                            <?php } ?>
                            <!-- b>Datos a fecha:</b> -->
                        </p>
                    </div>
                </caption>

                <thead>
                    <tr>
                        <?php
                            foreach ($anyos as $pos => $anyo) {
                                if ($pos === array_key_first($anyos)) {
                                    echo "<th scope='col'></th>\n";
                                } else {
                                    echo '<th scope="col" style="text-align: right;">' . $anyo . "</th>\n";
                                }
                            }
                        ?>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($datos as $registro) {
                        echo "<tr>\n";
                        foreach ($registro as $pos => $dato) {
                            if ($pos === array_key_first($registro)) {
                                echo "<td>" . $dato ."</td>\n";
                            } else {
                                echo '<td style="text-align: right;">' . $dato . "</td>\n";
                            }
                        }
                        echo "</tr>\n";
                    } ?>
                </tbody>
            </table>
        </div><br />
    <?php
    }
}