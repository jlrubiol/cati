<?php

foreach ($centros as $centro) :
    $globales_del_centro = array_filter(
        $globales, function ($fila) use (&$centro) {
            return $fila['COD_CENTRO'] === $centro->id;
        }
    );
    if (!$globales_del_centro) {
        continue;
    }

    $anyos = "'".implode(
        "','", array_map(
            function ($gc) {
                return $gc['ANO_ACADEMICO'].'–'.($gc['ANO_ACADEMICO'] + 1);
            }, $globales_del_centro
        )
    )."'";
    $matriculados = implode(',', array_column($globales_del_centro, 'ALUMNOS_ADAPTA_GRADO_MATRI'));
    $matriculados_ni = implode(',', array_column($globales_del_centro, 'ALUMNOS_ADAPTA_GRADO_MATRI_NI'));
    $titulados = implode(',', array_column($globales_del_centro, 'ALUMNOS_ADAPTA_GRADO_TITULADO'));

    if (!preg_match('/wkhtmltopdf/', $_SERVER['HTTP_USER_AGENT'])) {
        ?>
        <div id="container<?php echo $centro->id ?>" class='container' style="width: 75%;">
            <canvas id="canvas_gag_<?php echo $centro->id ?>"></canvas>  <!-- width="400" height="400" -->
        </div><?php

    }
    ?>

    <script>
    var mydata_gag_<?php echo $centro->id ?> = {
        labels: [<?php echo $anyos ?>],
        datasets: [{
            type: 'line',
            lineTension: 0, // línea recta
            fill: false,
            label: '<?php echo Yii::t('cati', 'Matriculados en cursos de adaptación al grado') ?>',
            backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(), // 'rgba(255, 99, 132, 0.2)',
            borderColor: window.chartColors.red, // 'rgba(255,99,132,1)',
            borderWidth: 1,
            data: [ <?php echo $matriculados ?> ]
        }, {
            type: 'bar',
            // lineTension: 0, // línea recta
            // fill: false,
            label: '<?php echo Yii::t('cati', 'Matriculados nuevos en cursos de adaptación al grado') ?>',
            backgroundColor: color(window.chartColors.purple).alpha(0.5).rgbString(),
            borderColor: window.chartColors.purple,
            data: [ <?php echo $matriculados_ni ?> ]
        }, {
            type: 'bar',
            label: '<?php echo Yii::t('cati', 'Titulados en cursos de adaptación al grado') ?>',
            backgroundColor: color(window.chartColors.orange).alpha(0.5).rgbString(),
            borderColor: window.chartColors.orange,
            data: [ <?php echo $titulados ?> ]
        }]
    };
    var chartConfig_gag_<?php echo $centro->id ?> = JSON.parse(JSON.stringify(chartConfig));
    chartConfig_gag_<?php echo $centro->id ?>.options.title.text = '<?php echo $estudio->nombre ?> — <?php echo $centro->nombre ?>';
    chartConfig_gag_<?php echo $centro->id ?>.data = mydata_gag_<?php echo $centro->id ?>;

    function grafica_gag_<?php echo $centro->id ?>() {
        var ctx<?php echo $centro->id ?> = document.getElementById("canvas_gag_<?php echo $centro->id ?>").getContext("2d");

        var myChart_gag_<?php echo $centro->id ?> = new Chart(
            ctx<?php echo $centro->id ?>,
            chartConfig_gag_<?php echo $centro->id ?>
        );
    };
    addLoadEvent(grafica_gag_<?php echo $centro->id ?>);
    </script>

    <table class="table table-striped table-bordered">

    <caption style='text-align: center;'>
        <b><?php echo Yii::t('cati', 'Titulación') ?>:</b> <?php echo $estudio->nombre ?><br>
        <b><?php echo Yii::t('cati', 'Centro') ?>:</b> <?php echo $centro->nombre ?><br>
        <b><?php echo Yii::t('cati', 'Datos a fecha') ?>:</b> <?php echo date('d-m-Y', strtotime($globales[count($globales) - 1]['FECHA_CARGA'])) ?>
    </caption>

    <thead><tr>
        <td><b><?php echo Yii::t('cati', 'Curso') ?></b></td>
        <td style='text-align: right;'><b><?php echo Yii::t('cati', 'Matriculados en cursos de adaptación al grado') ?></b></td>
        <td style='text-align: right;'><b><?php echo Yii::t('cati', 'Matriculados nuevos en cursos de adaptación al grado') ?></b></td>
        <td style='text-align: right;'><b><?php echo Yii::t('cati', 'Titulados en cursos de adaptación al grado') ?></b></td>
    </tr></thead>

    <?php
    foreach ($globales_del_centro as $datos_del_anyo) {
        ?>
        <tr>
            <td><?php echo $datos_del_anyo['ANO_ACADEMICO'] ?>–<?php echo $datos_del_anyo['ANO_ACADEMICO'] + 1 ?></td>
            <td style='text-align: right;'><?php echo $datos_del_anyo['ALUMNOS_ADAPTA_GRADO_MATRI'] ?></td>
            <td style='text-align: right;'><?php echo $datos_del_anyo['ALUMNOS_ADAPTA_GRADO_MATRI_NI'] ?></td>
            <td style='text-align: right;'><?php echo $datos_del_anyo['ALUMNOS_ADAPTA_GRADO_TITULADO'] ?></td>
        </tr>
        <?php

    } // endforeach
    ?>

    </table><br><br>
    <?php
endforeach;
