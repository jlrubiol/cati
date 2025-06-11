<?php

foreach ($centros as $num_centro => $centro) :
    $globales_del_centro = array_filter(
        $globales_abandono, function ($fila) use (&$centro) {
            return $fila['COD_CENTRO'] === $centro->id;
        }
    );
    if (!$globales_del_centro) {
        continue;
    }

    $subnum_tabla = $num_centro + 1;
    $titulo = (isset($apartado) ? "Tabla {$apartado}.{$num_tabla}.{$subnum_tabla}: " : '' ) . Yii::t('cati', 'Tasas de abandono/graduación');
    $anyos = "'" . implode(
        "','", array_map(
            function ($gc) {
                return $gc['ANO_ACADEMICO'] . '–' . ($gc['ANO_ACADEMICO'] + 1);
            }, $globales_del_centro
        )
    ) . "'";
    $abandonos = implode(',', array_column($globales_del_centro, 'TASA_ABANDONO'));
    $graduaciones = implode(',', array_column($globales_del_centro, 'TASA_GRADUACION'));

    if (!preg_match('/wkhtmltopdf/', $_SERVER['HTTP_USER_AGENT'])) {
        ?>
        <div id="container_ga_<?php echo $centro->id ?>" class='container' style="width: 75%;">
            <canvas id="canvas_ga_<?php echo $centro->id ?>"></canvas>  <!-- width="400" height="400" -->
        </div><?php
    } ?>

    <script>
    var mydata_ga_<?php echo $centro->id ?> = {
        labels: [<?php echo $anyos ?>],
        datasets: [{
            type: 'line',
            lineTension: 0, // línea recta
            fill: false,
            label: '<?php echo Yii::t('cati', 'Abandono') ?>',
            backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(), // 'rgba(255, 99, 132, 0.2)',
            borderColor: window.chartColors.red, // 'rgba(255,99,132,1)',
            borderWidth: 1,
            data: [ <?php echo $abandonos ?> ]
        }, {
            type: 'bar',
            // lineTension: 0, // línea recta
            // fill: false,
            label: '<?php echo Yii::t('cati', 'Graduación') ?>',
            backgroundColor: color(window.chartColors.purple).alpha(0.5).rgbString(),
            borderColor: window.chartColors.purple,
            data: [ <?php echo $graduaciones ?> ]
        }]
    };
    var chartConfig_ga_<?php echo $centro->id ?> = JSON.parse(JSON.stringify(chartConfig));
    chartConfig_ga_<?php echo $centro->id ?>.options.title.text = '<?php echo $estudio->nombre ?> — <?php echo $centro->nombre ?>';
    chartConfig_ga_<?php echo $centro->id ?>.data = mydata_ga_<?php echo $centro->id ?>;

    function grafica_ga_<?php echo $centro->id ?>() {
        var ctx_ga_<?php echo $centro->id ?> = document.getElementById("canvas_ga_<?php echo $centro->id ?>").getContext("2d");

        var myChart_ga_<?php echo $centro->id ?> = new Chart(
            ctx_ga_<?php echo $centro->id ?>,
            chartConfig_ga_<?php echo $centro->id ?>
        );
    };
    addLoadEvent(grafica_ga_<?php echo $centro->id ?>);
    </script>

    <div class='table-responsive'>
        <table class="table table-striped table-hover cabecera-azul compact" style="width: 100%;">

        <caption style='text-align: center; color: #777;'>
            <p style="font-size: 140%;"><?php echo $titulo ?></p>
            <p>
                <b><?php echo Yii::t('cati', 'Titulación') ?>:</b> <?php echo $estudio->nombre ?><br>
                <b><?php echo Yii::t('cati', 'Centro') ?>:</b> <?php echo $centro->nombre ?><br>
                <b><?php echo Yii::t('cati', 'Datos a fecha') ?>:</b> <?php echo date('d-m-Y', strtotime($globales_abandono[count($globales_abandono) - 1]['FECHA_CARGA'])) ?>
            </p>
        </caption>

        <thead>
            <tr>
                <th><b><?php echo Yii::t('cati', 'Curso de la cohorte de nuevo ingreso') ?> (*)</b></th>
                <th style='text-align: right;'><b><?php echo Yii::t('cati', 'Abandono') ?></b></th>
                <th style='text-align: right;'><b><?php echo Yii::t('cati', 'Graduación') ?></b></th>
            </tr>
        </thead>

        <?php
        foreach ($globales_del_centro as $datos_del_anyo) {
            ?>
            <tr>
                <td><?php echo $datos_del_anyo['ANO_ACADEMICO'] ?>–<?php echo $datos_del_anyo['ANO_ACADEMICO'] + 1 ?></td>
                <td style='text-align: right;'><?php echo $datos_del_anyo['TASA_ABANDONO'] ?></td>
                <td style='text-align: right;'><?php echo $datos_del_anyo['TASA_GRADUACION'] ?></td>
            </tr>
            <?php
        } // endforeach
        ?>

    </table>

    <p>(*) <?php echo Yii::t(
        'cati', 'El curso de la cohorte de nuevo ingreso muestra el curso académico de inicio de un'
        . ' conjunto de estudiantes que acceden a una titulación por preinscripción. Los datos de la tasa de graduación y'
        . " abandono de una cohorte en el curso académico 'x' estarán disponibles a partir del curso 'x+n', donde 'n' es la"
        . ' duración en años del plan de estudios.'
           ) ?>
    </p></div><br>
    <?php
endforeach;
