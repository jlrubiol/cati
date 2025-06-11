<?php

/*
 * Tasas de éxito/rendimiento/eficiencia
 */

foreach ($centros as $num_centro => $centro) :
    $globales_del_centro = array_filter(
        $globales, function ($fila) use (&$centro) {
            return $fila['COD_CENTRO'] === $centro->id;
        }
    );
    if (!$globales_del_centro) {
        continue;
    }

    $subnum_tabla = $num_centro + 1;
    $titulo = (isset($apartado) ? "Tabla {$apartado}.{$num_tabla}.{$subnum_tabla}: " : '' ) . Yii::t('cati', 'Tasas de éxito/rendimiento/eficiencia');

    $anyos = "'".implode(
        "','", array_map(
            function ($gc) {
                return $gc['ANO_ACADEMICO'].'–'.($gc['ANO_ACADEMICO'] + 1);
            }, $globales_del_centro
        )
    )."'";
    $exitos = implode(',', array_column($globales_del_centro, 'TASA_EXITO'));
    $rendimientos = implode(',', array_column($globales_del_centro, 'TASA_RENDIMIENTO'));
    $eficiencias = implode(',', array_column($globales_del_centro, 'TASA_EFICIENCIA'));
?>

    <script>
    var mydata_ge_<?php echo $centro->id ?> = {
        labels: [<?php echo $anyos ?>],
        datasets: [{
            type: 'bar',
            lineTension: 0, // línea recta
            fill: false,
            label: '<?php echo Yii::t('cati', 'Éxito') ?>',
            backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(), // 'rgba(255, 99, 132, 0.2)',
            borderColor: window.chartColors.red, // 'rgba(255,99,132,1)',
            borderWidth: 1,
            data: [ <?php echo $exitos ?> ]
        }, {
            type: 'bar',
            // lineTension: 0, // línea recta
            // fill: false,
            label: '<?php echo Yii::t('cati', 'Rendimiento') ?>',
            backgroundColor: color(window.chartColors.purple).alpha(0.5).rgbString(),
            borderColor: window.chartColors.purple,
            data: [ <?php echo $rendimientos ?> ]
        }, {
            type: 'bar',
            label: '<?php echo Yii::t('cati', 'Eficiencia') ?>',
            backgroundColor: color(window.chartColors.orange).alpha(0.5).rgbString(),
            borderColor: window.chartColors.orange,
            data: [ <?php echo $eficiencias ?> ]
        }]
    };

    var chartConfig_ge_<?php echo $centro->id ?> = JSON.parse(JSON.stringify(chartConfig));
    chartConfig_ge_<?php echo $centro->id ?>.options.title.text = '<?php echo $estudio->nombre ?> — <?php echo $centro->nombre ?>';
    chartConfig_ge_<?php echo $centro->id ?>.data = mydata_ge_<?php echo $centro->id ?>;

    function grafica_ge_<?php echo $centro->id ?>() {
        var ctx_ge_<?php echo $centro->id ?> = document.getElementById("canvas_ge_<?php echo $centro->id ?>").getContext("2d");

        var myChart_ge_<?php echo $centro->id ?> = new Chart(
            ctx_ge_<?php echo $centro->id ?>,
            chartConfig_ge_<?php echo $centro->id ?>
        );
    };
    addLoadEvent(grafica_ge_<?php echo $centro->id ?>);
    </script>

    <div class='table-responsive'>
        <table class="table table-striped table-hover cabecera-azul compact" style="width: 100%;">

        <caption style='text-align: center; color: #777;'>
            <p style="font-size: 140%;"><?php echo $titulo ?></p>
            <p>
                <b><?php echo Yii::t('cati', 'Titulación') ?>:</b> <?php echo $estudio->nombre ?><br>
                <b><?php echo Yii::t('cati', 'Centro') ?>:</b> <?php echo $centro->nombre ?><br>
                <b><?php echo Yii::t('cati', 'Datos a fecha') ?>:</b> <?php echo date('d-m-Y', strtotime($globales[count($globales) - 1]['FECHA_CARGA'])) ?>
            </p>
        </caption>

        <thead>
            <tr>
                <th><b><?php echo Yii::t('cati', 'Curso') ?></b></th>
                <th style='text-align: right;'><b><?php echo Yii::t('cati', 'Éxito') ?></b></th>
                <th style='text-align: right;'><b><?php echo Yii::t('cati', 'Rendimiento') ?></b></th>
                <th style='text-align: right;'><b><?php echo Yii::t('cati', 'Eficiencia') ?></b></th>
            </tr>
        </thead>

        <?php
        foreach ($globales_del_centro as $datos_del_anyo) {
            ?>
            <tr>
                <td><?php echo $datos_del_anyo['ANO_ACADEMICO'] ?>–<?php echo $datos_del_anyo['ANO_ACADEMICO'] + 1 ?></td>
                <td style='text-align: right;'><?php echo $datos_del_anyo['TASA_EXITO'] ?></td>
                <td style='text-align: right;'><?php echo $datos_del_anyo['TASA_RENDIMIENTO'] ?></td>
                <td style='text-align: right;'><?php echo $datos_del_anyo['TASA_EFICIENCIA'] ?></td>
            </tr>
            <?php

        } // endforeach
        ?>

        </table>
    </div>
    <br><br>

    <?php
    if (!preg_match('/wkhtmltopdf/', $_SERVER['HTTP_USER_AGENT'])) {
        ?>
        <div id="container_ge_<?php echo $centro->id ?>" class='container' style="width: 75%;">
            <canvas id="canvas_ge_<?php echo $centro->id ?>"></canvas>  <!-- width="400" height="400" -->
        </div><?php
    }
endforeach;
