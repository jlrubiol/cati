<?php
/**
 * Vista de un estudio de grado o máster.
 *
 * @author  Enrique Matías Sánchez <quique@unizar.es>
 * @license GPL-3.0+
 *
 * @see https://gitlab.unizar.es/titulaciones/cati
 */
use app\assets\VerticalTabsAsset;
use yii\helpers\Html;

$bundle = VerticalTabsAsset::register($this);

$this->title = $estudio->nombre;
$this->registerMetaTag([
    'name' => 'description',
    'content' => sprintf(
        Yii::t(
            'cati',
            'Toda la información sobre el estudio de %s: Porqué cursar esta titulación,'
            . ' acceso y admisión, plan de estudios, asignaturas y profesorado, etc.'
        ),
        Html::encode($estudio->nombre)
    ),
]);
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>
    <?php echo Html::encode($this->title); ?>
    <small><?php printf('%d–%d', $anyo_academico, $anyo_academico + 1); ?></small>
</h1>

<hr><br>

<div class="row breadcrumb">
    <div class="field__label">
        <?php echo Yii::t('cati', 'Centros de impartición'); ?>
    </div><br>
    <?php echo $this->render('_centros2', [
        'anyo_academico' => $anyo_academico,
        'coordinadores' => $coordinadores,
        'estudio' => $estudio,
        'planes_por_centro' => $planes_por_centro,
    ]); ?>
    </div><br>

<?php if ($es_curso_actual) : ?>
<div class="container">
    <div class="row">
        <div class="col-sm-3 col-md-3">
            <?php echo $this->render('_tabs', [
                'estudio' => $estudio,
            ]); ?>
        </div>
        <div class="col-sm-9 col-md-9">
            <!-- div class="well" -->
            <?php echo $this->render('_tabpanes', [
                'anyo_academico' => $anyo_academico,
                'anyo_profesorado' => $anyo_profesorado,
                'estudio' => $estudio,
                'paginas' => $paginas,
                'planes' => $planes,
                'version_informe' => $version_informe,
                'version_plan' => $version_plan,
            ]); ?>
            <!-- /div --> <!-- well -->
        </div> <!-- right side -->
    </div> <!-- row -->
</div> <!-- container -->
<?php endif; ?>
