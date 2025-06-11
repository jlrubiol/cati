<?php

$subnum_tabla = 0;
foreach ($centros as $centro) :
    $globales_del_centro = array_filter(
        $globales, function ($fila) use ($centro) {
            return $fila['COD_CENTRO'] === $centro->id;
        }
    );
    if (!$globales_del_centro) {
        continue;
    }
    $subnum_tabla++;
    $titulo = (isset($apartado) ? "Tabla {$apartado}.{$num_tabla}.{$subnum_tabla}: " : '' ) . Yii::t('cati', 'Oferta/Nuevo ingreso/Matrícula');

    $anyos = "'".implode(
        "','", array_map(
            function ($gc) {
                return $gc['ANO_ACADEMICO'].'–'.($gc['ANO_ACADEMICO'] + 1);
            }, $globales_del_centro
        )
    )."'";

    $matriculados = implode(',', array_column($globales_del_centro, 'ALUMNOS_MATRICULADOS'));
    $nuevo_ingreso = implode(',', array_column($globales_del_centro, 'ALUMNOS_NUEVO_INGRESO'));
    $titulados = implode(',', array_column($globales_del_centro, 'ALUMNOS_GRADUADOS'));
?>

    <script>
    var mydata_gni_<?= $centro->id ?> = {
        labels: [<?= $anyos ?>],
        datasets: [{
            type: 'line',
            lineTension: 0, // línea recta
            fill: false,
            label: '<?= Yii::t('cati', 'Estudiantes matriculados') ?>',
            backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(), // 'rgba(255, 99, 132, 0.2)',
            borderColor: window.chartColors.red, // 'rgba(255,99,132,1)',
            borderWidth: 1,
            data: [ <?= $matriculados ?> ]
        }, {
            type: 'bar',
            label: '<?= Yii::t('cati', 'Estudiantes nuevo ingreso') ?>',
            backgroundColor: color(window.chartColors.purple).alpha(0.5).rgbString(),
            borderColor: window.chartColors.purple,
            data: [ <?= $nuevo_ingreso ?> ]
        }, {
            type: 'bar',
            label: '<?= Yii::t('cati', 'Titulados') ?>',
            backgroundColor: color(window.chartColors.orange).alpha(0.5).rgbString(),
            borderColor: window.chartColors.orange,
            data: [ <?= $titulados ?> ]
        }]
    };

    var chartConfig_gni_<?= $centro->id ?> = JSON.parse(JSON.stringify(chartConfig));
    chartConfig_gni_<?= $centro->id ?>.options.title.text = '<?= $estudio->nombre ?> — <?= $centro->nombre ?>';
    chartConfig_gni_<?= $centro->id ?>.data = mydata_gni_<?= $centro->id ?>;

    function grafica_gni_<?= $centro->id ?>() {
        var ctx_gni_<?= $centro->id ?> = document.getElementById("canvas_gni_<?= $centro->id ?>").getContext("2d");

        var myChart_gni_<?= $centro->id ?> = new Chart(
            ctx_gni_<?= $centro->id ?>,
            chartConfig_gni_<?= $centro->id ?>
        );
    };
    addLoadEvent(grafica_gni_<?= $centro->id ?>);
    </script>

    <div class='table-responsive'>
    <table class="table table-striped table-hover cabecera-azul compact" style="width: 100%;">

    <caption style='text-align: center; color: #777;'>
        <p style="font-size: 140%;"><?= $titulo ?></p>
        <p>
            <b><?= Yii::t('cati', 'Titulación') ?>:</b> <?= $estudio->nombre ?><br>
            <b><?= Yii::t('cati', 'Centro') ?>:</b> <?= $centro->nombre ?><br>
            <b><?= Yii::t('cati', 'Datos a fecha') ?>:</b> <?= date('d-m-Y', strtotime($globales[count($globales) - 1]['FECHA_CARGA'])) ?>
        </p>
    </caption>

    <thead>
        <tr>
            <th><b><?= Yii::t('cati', 'Curso') ?></b></th>
            <th style='text-align: right;'><b><?= Yii::t('cati', 'Plazas ofertadas') ?></b></th>
            <th style='text-align: right;'><b><?= Yii::t('cati', 'Estudiantes nuevo ingreso') ?></b></th>
            <th style='text-align: right;'><b><?= Yii::t('cati', 'Estudiantes matriculados') ?></b></th>
            <th style='text-align: right;'><b><?= Yii::t('cati', 'Titulados') ?></b></th>
        </tr>
    </thead>

    <?php
    foreach ($globales_del_centro as $datos_del_anyo) {
        ?>
        <tr>
            <td><?= $datos_del_anyo['ANO_ACADEMICO'] ?>–<?= $datos_del_anyo['ANO_ACADEMICO'] + 1 ?></td>
            <td style='text-align: right;'><?= $datos_del_anyo['PLAZAS_OFERTADAS'] ?></td>
            <td style='text-align: right;'><?= $datos_del_anyo['ALUMNOS_NUEVO_INGRESO'] ?></td>
            <td style='text-align: right;'><?= $datos_del_anyo['ALUMNOS_MATRICULADOS'] ?></td>
            <td style='text-align: right;'><?= $datos_del_anyo['ALUMNOS_GRADUADOS'] ?></td>
        </tr>
    <?php

    } // endforeach
    ?>

    </table>
</div><br><br>

<?php
    if (!preg_match('/wkhtmltopdf/', $_SERVER['HTTP_USER_AGENT'])) {
        ?>
        <div id="container_gni_<?= $centro->id ?>" class='container' style="width: 75%;">
            <canvas id="canvas_gni_<?= $centro->id ?>"></canvas>  <!-- width="400" height="400" -->
        </div><?php

    }
endforeach;
