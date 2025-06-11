<?php
use app\models\Centro;
use app\models\Estudio;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

$anyo = $model['ANO_ACADEMICO'];
$centro = Centro::findOne($model['COD_CENTRO']);
$estudio = Estudio::getEstudio($model->COD_ESTUDIO);

$atributos = $model->getAttributes();
$estudios_previos = null;
foreach ($atributos as $clave => $valor) {
    if ('EP' == substr($clave, 0, 2) and !in_array($clave, ['EP_EXTRANJERO', 'EP_COU']) and ($clave != 'EP_NO_CONSTA' or $valor != null)) {
        $estudios_previos[] = ['clave' => $clave, 'valor' => $valor];
    }
}

$alumnos = array_column($estudios_previos, 'valor');
$total = array_sum($alumnos);

# Gráfica
$conceptos = array_column($estudios_previos, 'clave');
$conceptos = array_map(function ($c) use ($model) {
    return $model->getAttributeLabel($c);
}, $conceptos);

if (!preg_match('/wkhtmltopdf/', $_SERVER['HTTP_USER_AGENT'])) {
    ?>
    <div id="container_ep_<?php echo $centro->id; ?>" class='container' style="width: 60%;">
        <canvas id="canvas_ep_<?php echo $centro->id; ?>" style="height: 300"></canvas>
    </div><?php
} ?>

<script>
var mydata_ep_<?php echo $centro->id; ?> = {
    labels: <?php echo json_encode($conceptos); ?>,
    datasets: [{
        label: '<?php echo Yii::t('cati', 'Estudio previo'); ?>',
        data:  <?php echo json_encode($alumnos); ?> ,
        backgroundColor: Object.keys(window.chartColors).map(key => window.chartColors[key]),
        borderColor: 'rgba(255,255,255,1)',  // white
        borderWidth: 1,
    }]
};

function grafica_ep_<?php echo $centro->id; ?>() {
    var ctx_ep_<?php echo $centro->id; ?>
      = document.getElementById("canvas_ep_<?php echo $centro->id; ?>").getContext("2d");

    var myChart_ep_<?php echo $centro->id; ?> = new Chart(
        ctx_ep_<?php echo $centro->id; ?>,
        {
            type: 'doughnut',
            data: mydata_ep_<?php echo $centro->id; ?>,
            options: {
                aspectRatio: 1,
                legend: {
                    position: 'right',
                    labels: {fontSize: 16,},
                },
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: '<?php echo Html::encode($estudio->nombre).' — '.Html::encode($centro->nombre); ?>'
                }
            }
        }
    );
};
addLoadEvent(grafica_ep_<?php echo $centro->id; ?>);
</script>

<?php
# Tabla
$titulo = Yii::t('cati', 'Estudio previo de los estudiantes de nuevo ingreso');
$anyo_acad = Yii::t('cati', 'Año académico');
$siguiente_anyo = $anyo + 1;
$est = Yii::t('cati', 'Estudio');
$nombre_est = Html::encode($estudio->nombre);
$cent = Yii::t('cati', 'Centro');
$nombre_cent = Html::encode($centro->nombre);
$fecha = Yii::t('cati', 'Datos a fecha');
$dato_fecha = date('d-m-Y', strtotime($model->A_FECHA));
$caption = <<< CAPTION
<div style='text-align: center; color: #777;'>
  <p style='font-size: 140%;'>$titulo</p>
  <p style='font-size: 120%;'>$anyo_acad: $anyo/$siguiente_anyo</p>
  <p><b>$est:</b> $nombre_est<br><b>$cent:</b> $nombre_cent<br><b>$fecha:</b> $dato_fecha</p>
</div>
CAPTION;

echo "<div class='table-responsive'>";
echo GridView::widget([
    'caption' => $caption,
    'columns' => [
        [
            'attribute' => 'clave',
            'label' => Yii::t('cati', 'Concepto'),
            'value' => function ($fila) use ($model) {
                $language = Yii::$app->request->cookies->getValue('language', 'es');

                return Yii::t('models', $model->getAttributeLabel($fila['clave']));
            },
        ], [
            'attribute' => 'valor',
            'contentOptions' => ['style' => 'text-align: right;'],
            'headerOptions' => ['style' => 'text-align: right;'],
            'label' => Yii::t('cati', 'Número de estudiantes'),
        ], [
            'contentOptions' => ['style' => 'text-align: right;'],
            'format' => ['percent', 1],
            'headerOptions' => ['style' => 'text-align: right;'],
            'label' => Yii::t('cati', 'Porcentaje'),
            'value' => function ($fila) use ($total) {
                return $fila['valor'] / $total;
            },
        ],
    ],
    'dataProvider' => new ArrayDataProvider(['allModels' => $estudios_previos]),
    'options' => ['class' => 'cabecera-azul'],
    'summary' => false,
    'tableOptions' => ['class' => 'table table-striped table-hover'],
]);
echo '(*) '.Yii::t('cati', 'Incluye los Estudios Extranjeros con credencial UNED').': &nbsp;';
echo Yii::t('cati', 'Nº estudiantes').': '.$model->EP_EXTRANJERO.'&nbsp;&nbsp;';
echo Yii::t('cati', 'Porcentaje').': '.round($model->EP_EXTRANJERO * 100 / $total, 1).'%';
echo '</div><br><hr><br>';
?>
