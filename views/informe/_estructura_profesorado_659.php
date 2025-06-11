<?php
use yii\helpers\Html;
use app\models\Centro;

$plan_id_nk = 415;
$estructuras = $estructuras[$plan_id_nk];
$totales_estructuras = $totales_estructuras[$plan_id_nk];

if (!$estructuras) {
    return;
}

foreach ([107, 202, 301] as $centro_id) :
    $centro = Centro::findOne($centro_id);
    $estructura = array_filter($estructuras, function ($registro) use ($centro_id) {
        return $registro['cod_centro'] == $centro_id;
    });

    if (!$estructura) {
        continue;
    }

    $totales = array_filter($totales_estructuras, function ($registro) use ($centro_id) {
        return $registro['cod_centro'] == $centro_id;
    });
    $total = reset($totales); ?>
    <div class="table-responsive cabecera-azul">
    <table class='table table-striped table-hover'>
    <caption style="text-align: center;">
        <p style="font-size: 140%;"><?php echo Yii::t('cati', 'Tabla de estructura del profesorado'); ?></p>
        <p style="font-size: 120%;">
            <?php echo Yii::t('cati', 'Año académico'); ?>: <?php echo $anyo; ?>/<?php echo $anyo + 1; ?>
        </p>
        <p>
        <?php
        printf(
            '<b>%s:</b> %s (%s %d)<br>',
            Yii::t('cati', 'Estudio'),
            Html::encode($nombre_estudio),
            Yii::t('cati', 'plan'),
            $plan_id_nk
        );
        printf('<b>%s:</b> %s<br>', Yii::t('cati', 'Centro'), Html::encode($centro->nombre));
        $fecha = reset($estructura)['fecha_carga'];
        $fecha_str = $fecha ? date('d-m-Y', strtotime($fecha))
                            : Yii::t('cati', 'Desconocida');
        printf('<b>%s:</b> %s<br>', Yii::t('cati', 'Datos a fecha'), $fecha_str); ?>
        </p>
    </caption>

    <thead>
    <tr>
        <th><?php echo Yii::t('cati', 'Categoría'); ?></th>
        <th style='text-align: right;'><?php echo Yii::t('cati', 'Total'); ?></th>
        <th style='text-align: right;'>%</th>
        <th style='text-align: right;'><?php echo Yii::t('cati', 'En primer curso'); ?></th>
        <th style='text-align: right;'><?php echo Yii::t('cati', 'Nº total sexenios'); ?></th>
        <th style='text-align: right;'><?php echo Yii::t('cati', 'Nº total quinquenios'); ?></th>
        <th style='text-align: right;'><?php echo Yii::t('cati', 'Horas impartidas'); ?></th>
        <th style='text-align: right;'>%</th>
    </tr>
    </thead>

    <tbody>
    <?php
    foreach ($estructura as $row) {
        /*
        $denom_length = mb_strpos($row['denom_categoria'], '(');
        if (!$denom_length) {
            $denom_length = mb_strlen($row['denom_categoria']) + 1;
        }
        */
        echo "<tr>\n";
        // echo '    <td>'.Yii::t('db', mb_substr($row['denom_categoria'], 0, $denom_length - 1))."</td>\n";
        echo '    <td>'.Yii::t('db', $row['denom_categoria'])."</td>\n";
        echo "    <td style='text-align: right;'>{$row['cantidad']}</td>\n";
        echo "    <td style='text-align: right;'>".percent($row['cantidad'], $total['cantidad'])."</td>\n";
        echo "    <td style='text-align: right;'>{$row['en_primero']}</td>\n";
        echo "    <td style='text-align: right;'>{$row['sexenios']}</td>\n";
        echo "    <td style='text-align: right;'>{$row['quinquenios']}</td>\n";
        echo "    <td style='text-align: right;'>{$row['horas_impartidas']}</td>\n";
        echo "    <td style='text-align: right;'>"
          .percent($row['horas_impartidas'], $total['horas_impartidas'])."</td>\n";
        echo "</tr>\n";
    } ?>
    </tbody>

    <tfoot style="font-weight: bold;">
    <tr>
        <td><?php echo Yii::t('cati', 'Total personal académico'); ?></td>
        <td style='text-align: right;'><?php echo $total['cantidad']; ?></td>
        <td style='text-align: right;'>100.0</td>
        <td style='text-align: right;'><?php echo $total['en_primero']; ?></td>
        <td style='text-align: right;'><?php echo $total['sexenios']; ?></td>
        <td style='text-align: right;'><?php echo $total['quinquenios']; ?></td>
        <td style='text-align: right;'><?php echo $total['horas_impartidas']; ?></td>
        <td style='text-align: right;'>100.0</td>
    </tr>
    </tfoot>
    </table>
    </div>
<?php
endforeach;
