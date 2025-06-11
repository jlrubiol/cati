<?php

$subnum_tabla = 0;
foreach ($centros as $centro) :
    $globales_del_centro = array_filter(
        $globales, function ($fila) use (&$centro) {
            return $fila['COD_CENTRO'] === $centro->id;
        }
    );
    if (!$globales_del_centro) {
        continue;
    }
    $subnum_tabla++;
    $titulo = (isset($apartado) ? "Tabla {$apartado}.{$num_tabla}.{$subnum_tabla}: " : '' ) . 'Créditos reconocidos';

    $anyos = "'".implode(
        "','", array_map(
            function ($gc) {
                return $gc['ANO_ACADEMICO'].'–'.($gc['ANO_ACADEMICO'] + 1);
            }, $globales_del_centro
        )
    )."'";
    $reconocidos = implode(',', array_column($globales_del_centro, 'CREDITOS_RECONOCIDOS'));
    $matriculados = implode(',', array_column($globales_del_centro, 'CREDITOS_MATRICULADOS'));
    $alumnos = implode(',', array_column($globales_del_centro, 'ALUMNOS_CON_RECONOCIMIENTO'));

    if (!preg_match('/wkhtmltopdf/', $_SERVER['HTTP_USER_AGENT'])) {
        ?>
        <div id="container_gc_<?php echo $centro->id ?>" class='container' style="width: 75%;">
            <canvas id="canvas_gc_<?php echo $centro->id ?>"></canvas>  <!-- width="400" height="400" -->
        </div><?php

    } ?>

    <script>
    var mydata_gc_<?php echo $centro->id ?> = {
        labels: [<?php echo $anyos ?>],
        datasets: [{
            type: 'line',
            lineTension: 0, // línea recta
            // fill: false,
            label: '<?php echo Yii::t('cati', 'Créditos reconocidos') ?>',
            backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(), // 'rgba(255, 99, 132, 0.2)',
            borderColor: window.chartColors.red, // 'rgba(255,99,132,1)',
            borderWidth: 1,
            data: [ <?php echo $reconocidos ?> ]
        }, {
            type: 'line',
            lineTension: 0, // línea recta
            // fill: false,
            label: '<?php echo Yii::t('cati', 'Créditos matriculados') ?>',
            backgroundColor: color(window.chartColors.purple).alpha(0.5).rgbString(),
            borderColor: window.chartColors.purple,
            data: [ <?php echo $matriculados ?> ]
        }, {
            type: 'bar',
            label: '<?php echo Yii::t('cati', 'Estudiantes con créditos reconocidos') ?>',
            backgroundColor: color(window.chartColors.orange).alpha(0.5).rgbString(),
            borderColor: window.chartColors.orange,
            data: [ <?php echo $alumnos ?> ]
        }]

    };

    var chartConfig_gc_<?php echo $centro->id ?> = JSON.parse(JSON.stringify(chartConfig));
    chartConfig_gc_<?php echo $centro->id ?>.options.title.text = '<?php echo $estudio->nombre ?> — <?php echo $centro->nombre ?>';
    chartConfig_gc_<?php echo $centro->id ?>.data = mydata_gc_<?php echo $centro->id ?>;

    function grafica_gc_<?php echo $centro->id ?>() {
        var ctx_gc_<?php echo $centro->id ?> = document.getElementById("canvas_gc_<?php echo $centro->id ?>").getContext("2d");

        var myChart_gc_<?php echo $centro->id ?> = new Chart(
            ctx_gc_<?php echo $centro->id ?>,
            chartConfig_gc_<?php echo $centro->id ?>
        );
    }
    addLoadEvent(grafica_gc_<?php echo $centro->id ?>);
    </script>

    <div class='table-responsive'>
        <table class="table table-striped table-hover cabecera-azul compact" style="width: 100%;">

        <caption style='text-align: center; color: #777;'>
            <p style='font-size: 140%;'><?= $titulo ?></p>
            <p>
                <b><?php echo Yii::t('cati', 'Estudio') ?>:</b> <?php echo $estudio->nombre ?><br>
                <b><?php echo Yii::t('cati', 'Centro') ?>:</b> <?php echo $centro->nombre ?><br>
                <b><?php echo Yii::t('cati', 'Datos a fecha') ?>:</b> <?php echo date('d-m-Y', strtotime($globales[count($globales) - 1]['FECHA_CARGA'])) ?>
            </p>
        </caption>

        <thead>
            <tr>
                <th><b><?php echo Yii::t('cati', 'Curso') ?></b></th>
                <th style='text-align: right;'><b><?php echo Yii::t('cati', 'Créditos reconocidos') ?></b></th>
                <th style='text-align: right;'><b><?php echo Yii::t('cati', 'Estudiantes con créditos reconocidos') ?></b></th>
                <th style='text-align: right;'><b><?php echo Yii::t('cati', 'Créditos matriculados') ?></b></th>
                <th style='text-align: right;'><b><?php echo Yii::t('cati', 'Porcentaje') ?></b></th>
            </tr>
        </thead>

        <?php
        foreach ($globales_del_centro as $datos_del_anyo) {
            ?>
            <tr>
                <td><?php echo $datos_del_anyo['ANO_ACADEMICO'] ?>–<?php echo $datos_del_anyo['ANO_ACADEMICO'] + 1 ?></td>
                <td style='text-align: right;'><?php echo $datos_del_anyo['CREDITOS_RECONOCIDOS'] ?></td>
                <td style='text-align: right;'><?php echo $datos_del_anyo['ALUMNOS_CON_RECONOCIMIENTO'] ?></td>
                <td style='text-align: right;'><?php echo $datos_del_anyo['CREDITOS_MATRICULADOS'] ?></td>
                <td style='text-align: right;'><?php echo calcularPorcentaje($datos_del_anyo) ?></td>
            </tr>
            <?php

        } // endforeach
        ?>

        </table>
    </div><br />
    <?php
endforeach;

function calcularPorcentaje($datos_del_anyo) {
    if (!$datos_del_anyo['CREDITOS_MATRICULADOS']) return '-';
    return round($datos_del_anyo['CREDITOS_RECONOCIDOS']/$datos_del_anyo['CREDITOS_MATRICULADOS'] * 100, 2);
}