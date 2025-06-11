<?php

use yii\helpers\ArrayHelper;

if (!$indicadores) {
    return;
}

$subnum_tabla = 1;  # 2a tabla después de Distribución de calificaciones
foreach ($centros as $centro) {
    $indicadores_del_centro = array_filter(
        $indicadores, function ($i) use (&$centro) {
            return $i->COD_CENTRO === $centro->id;
        }
    );
    if (!$indicadores_del_centro) {
        continue;
    }
    $subnum_tabla++;
    $titulo = (isset($apartado) ? "Tabla {$apartado}.{$num_tabla}.{$subnum_tabla}: " : '' ) . Yii::t('cati', 'Análisis de los indicadores del título');
    $year = $indicadores[0]->ANO_ACADEMICO;

    // En PHP < 7 no se puede usar array_column() directamente sobre un array de objetos.
    // Antes hay que convertir los objetos a arrays.
    $indicadores_del_centro_arr = ArrayHelper::toArray(
        $indicadores_del_centro, [
            'app\models\AsignaturaIndicador' => [
                'DENOM_ASIGNATURA',
                'ALUMNOS_NO_PRESENTADOS',
                'ALUMNOS_SUSPENDIDOS',
                'ALUMNOS_APROBADOS',
                'ALUMNOS_RECONOCIDOS',
                'TOTAL_ALUMNOS',
            ],
        ]
    );
    $asignaturas = '"'.implode('","', array_column($indicadores_del_centro_arr, 'DENOM_ASIGNATURA')).'"';
    $matriculados = implode(',', array_column($indicadores_del_centro_arr, 'TOTAL_ALUMNOS'));
    $reconocidos = implode(',', array_column($indicadores_del_centro_arr, 'ALUMNOS_RECONOCIDOS'));
    $aprobados = implode(',', array_column($indicadores_del_centro_arr, 'ALUMNOS_APROBADOS'));
    $suspendidos = implode(',', array_column($indicadores_del_centro_arr, 'ALUMNOS_SUSPENDIDOS'));
    $no_presentados = implode(',', array_column($indicadores_del_centro_arr, 'ALUMNOS_NO_PRESENTADOS'));

    if (!preg_match('/wkhtmltopdf/', $_SERVER['HTTP_USER_AGENT'])) {
        ?>
        <div id="container_it_<?php echo $centro->id ?>" class='container' style="width: 100%;">
            <canvas id="canvas_it_<?php echo $centro->id ?>" height="400"></canvas>
        </div><?php

    } ?>

    <script>
    // Los colores están definidos en views/informe/_chart_config.php
    var mydata_it_<?php echo $centro->id ?> = {
        labels: [<?php echo $asignaturas ?>],
        datasets: [{
            // type: 'bar',
            label: '<?php echo Yii::t('cati', 'No presentados') ?>',
            backgroundColor: color(window.chartColors.black).alpha(0.5).rgbString(),
            borderColor: window.chartColors.black,
            data: [ <?php echo $no_presentados ?> ]
        }, {
            label: '<?php echo Yii::t('cati', 'Suspendidos') ?>',
            backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
            borderColor: window.chartColors.red,
            data: [ <?php echo $suspendidos ?> ]
        }, {
            label: '<?php echo Yii::t('cati', 'Aprobados') ?>',
            backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
            borderColor: color(window.chartColors.green).alpha(0.5).rgbString(),
            data: [ <?php echo $aprobados ?> ]
        }, {
            label: '<?php echo Yii::t('cati', 'Reconocidos') ?>',
            backgroundColor: color(window.chartColors.yellow).alpha(0.5).rgbString(),
            borderColor: window.chartColors.yellow,
            data: [ <?php echo $reconocidos ?> ]
        }, {
            label: '<?php echo Yii::t('cati', 'Matriculados') ?>',
            backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
            borderColor: window.chartColors.blue,
            data: [ <?php echo $matriculados ?> ],
        }]
    };
    var chartConfig_it_<?php echo $centro->id ?> = JSON.parse(JSON.stringify(chartConfig));
    chartConfig_it_<?php echo $centro->id ?>.options.title.text = '<?php echo $estudio->nombre ?> — <?php echo $centro->nombre ?>';
    chartConfig_it_<?php echo $centro->id ?>.options.legend.position = 'bottom';
    chartConfig_it_<?php echo $centro->id ?>.type = 'horizontalBar';
    chartConfig_it_<?php echo $centro->id ?>.data = mydata_it_<?php echo $centro->id ?>;

    function grafica_it_<?php echo $centro->id ?>() {
        var ctx_it_<?php echo $centro->id ?> = document.getElementById("canvas_it_<?php echo $centro->id ?>").getContext("2d");

        var myChart_it_<?php echo $centro->id ?> = new Chart(
            ctx_it_<?php echo $centro->id ?>,
            chartConfig_it_<?php echo $centro->id ?>
        );
    };
    addLoadEvent(grafica_it_<?php echo $centro->id ?>);
    </script><br />

    <div class='table-responsive'>
        <table class='table table-striped table-hover cabecera-azul compact indicadores' style="width: 100%;">

            <caption style='text-align: center; color: #777;'>
                <!-- p style="font-size: 130%;">Datos académicos de la Universidad de Zaragoza</p -->
                <p style="font-size: 140%;"><?php echo $titulo ?></p>
                <p style="font-size: 120%;"><?php echo Yii::t('cati', 'Año académico') ?>: <?php echo $year ?>/<?php echo ($year + 1) ?></p>

                <p>
                    <b><?php echo Yii::t('cati', 'Titulación') ?>:</b> <?php echo $estudio->nombre ?><br>
                    <b><?php echo Yii::t('cati', 'Centro') ?>:</b> <?php echo $centro->nombre ?><br>
                    <b><?php echo Yii::t('cati', 'Datos a fecha') ?>:</b> <?php echo date('d-m-Y', strtotime($indicadores[0]->A_FECHA))?>
                </p>
            </caption>

            <thead>
                <tr>
                    <th><?php echo Yii::t('cati', 'Curso') ?></th>
                    <th><?php echo Yii::t('cati', 'Cód As') ?></th>
                    <th><?php echo Yii::t('cati', 'Asignatura') ?></th>
                    <th><?php echo Yii::t('cati', 'Mat') ?></th>
                    <th><?php echo Yii::t('cati', 'Rec Equi Conv') ?></th>
                    <th><?php echo Yii::t('cati', 'Apro') ?></th>
                    <th><?php echo Yii::t('cati', 'Susp') ?></th>
                    <th><?php echo Yii::t('cati', 'No pre') ?></th>
                    <th><?php echo Yii::t('cati', 'Tasa éxito') ?></th>
                    <th><?php echo Yii::t('cati', 'Tasa rend') ?></th>
                </tr>
            </thead>

            <tbody>
                <tr><td colspan='10'><b>Cód As</b>: Código Asignatura | <b>Mat</b>: Matriculados | <b>Apro</b>: Aprobados | <b>Susp</b>: Suspendidos | <b>No Pre</b>: No presentados | <b>Tasa Rend</b>: Tasa Rendimiento</td></tr>
                <?php
                foreach ($indicadores_del_centro as $indicador) {
                    ?>
                    <tr>
                        <td><?php echo $indicador->PRELA_CU ?></td>
                        <td><?php echo $indicador->COD_ASIGNATURA ?></td>
                        <td><?php echo $indicador->DENOM_ASIGNATURA ?></td>
                        <td><?php echo $indicador->TOTAL_ALUMNOS ?></td>
                        <td><?php echo $indicador->ALUMNOS_RECONOCIDOS ?></td>
                        <td><?php echo $indicador->ALUMNOS_APROBADOS ?></td>
                        <td><?php echo $indicador->ALUMNOS_SUSPENDIDOS ?></td>
                        <td><?php echo $indicador->ALUMNOS_NO_PRESENTADOS ?></td>
                        <td><?php echo number_format($indicador->TASA_EXITO, 2, '.', '') ?></td>
                        <td><?php echo number_format($indicador->TASA_RENDIMIENTO, 2, '.', '') ?></td>
                    </tr>
                    <?php

                } // endforeach indicadores
                ?>
            </tbody>
        </table>
    </div>
<?php
} // endforeach centros
