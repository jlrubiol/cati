<?php
/**
 * Vista de un programa de doctorado.
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
$this->registerMetaTag(
    [
    'name' => 'description',
    'content' => sprintf(
        Yii::t(
            'cati',
            'Toda la información sobre el estudio de %s: Porqué cursar esta
              titulación, acceso y admisión, plan de estudios, asignaturas y
              profesorado, etc.'
        ),
        Html::encode($estudio->nombre)
    ),
    ]
);
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
    <?php echo $this->render(
        'doct/_centros_doct', [
            'planes_por_centro' => $planes_por_centro,
        ]
    ); ?>
</div><br> <!-- row -->

<div class="container">
    <div class="row">
        <div class="col-sm-3 col-md-3">
            <?php echo $this->render(
                'doct/_tabs_doct', [
                    'estudio' => $estudio,
                ]
            ); ?>
        </div>
        <div class="col-sm-9 col-md-9">
            <?php echo $this->render(
                'doct/_tabpanes_doct', [
                    'anyo_academico' => $anyo_academico,
                    'datos' => $datos,
                    'estudio' => $estudio,
                    'lineas' => $lineas,
                    'miembros_equipos' => $miembros_equipos,
                    'nombres_equipos' => $nombres_equipos,
                    'paginas' => $paginas,
                    'version_informe' => $version_informe,
                    'version_plan' => $version_plan,
                ]
            ); ?>
        </div> <!-- right side -->
    </div> <!-- row -->
</div> <!-- container -->
