<?php
use app\models\Calendario;
use app\models\Estudio;
use yii\helpers\Html;

$this->title = sprintf(Yii::t('cati', 'Rama de conocimiento: %s'), $rama->nombre);
$this->registerMetaTag([
    'name' => 'description',
    'content' => sprintf(
        Yii::t('cati', 'Listado de las titulaciones de %s en la Universidad de Zaragoza'),
        Html::encode($rama->nombre)
    ),
]);
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?php echo Html::encode($rama->nombre); ?>
    <small><?php printf("%s %d-%d", Yii::t('cati', 'Curso'), $anyo, $anyo + 1); ?></small>
</h1>

<hr>

<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="menu-curso" data-toggle="dropdown">
        <?php echo Yii::t('cati', 'Curso'); ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu-curso">
        <?php
        echo "<li role='presentation'>" . Html::a(
            Yii::t('cati', 'Anteriores'),
            'http://titulaciones.unizar.es/index2.php',
            ['target' => '_blank']
        ) . "</li>\n";

        for ($anyo = 2017; $anyo <= Calendario::getAnyoAcademico(); $anyo++) {
            echo '<li role="presentation">' . Html::a(
                $anyo . '/' . ($anyo + 1),
                ['lista-rama', 'rama_id' => $rama->id, 'anyo_academico' => $anyo],
                ['role' => 'menuitem']
            ) . "</li>\n";
        }
        ?>
    </ul>
</div><br>

<?php
foreach ($estudios as $tipo => $estudiostipo) :
    printf("<h3>%s</h3>\n", Yii::t('cati', $tipo));
    echo "<ul class='listado'>\n";
    foreach ($estudiostipo as $estudio) {
        $vista = (Estudio::DOCT_TIPO_ESTUDIO_ID != $estudio->tipoEstudio_id) ? 'ver' : 'ver-doct';
        echo '<li>' . Html::a(
            Html::encode($estudio->nombre),
            [$vista, 'id' => $estudio->id_nk, 'anyo_academico' => $estudio->anyo_academico]
        ) . "</li>\n";
    }
    echo "</ul>\n\n";
endforeach;
