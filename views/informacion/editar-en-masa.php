<?php
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use marqu3s\summernote\Summernote;

Icon::map($this, Icon::FA); // Maps the Font Awesome icon font framework

$this->title = Yii::t('cati', 'Edición de la información') . ' — ' . $tipoEstudio->nombre;
$this->params['breadcrumbs'][] = ['label' => Yii::t('gestion', 'Gestión'), 'url' => ['gestion/index']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('cati', 'Edición de la información') . ' — ' . $tipoEstudio->nombre,
    'url' => ['informacion/editar-infos-en-masa', 'tipoEstudio_id' => $tipoEstudio->id],
];
$this->params['breadcrumbs'][] = Yii::t('db', $seccion->titulo);

// Change background color
$this->registerCssFile('@web/css/gestion.css', ['depends' => 'app\assets\AppAsset']);
$locale = Yii::$app->catilanguage->getLocale(Yii::$app->language);
?>

<h1><?php echo Html::encode($this->title); ?></h1>
<h2><?php echo Yii::t('db', $seccion->titulo); ?></h2>
<hr><br>

<?php
echo Alert::widget([
    'body' => "<div style='display: block; float: left; margin-right: 10px;'>"
        . Icon::show('exclamation-triangle', ['class' => 'fa-3x'], Icon::FA)
        . "</div>\n"
        . '<div><strong>' . Yii::t('gestion', '¡Atención!') . "</strong><br>\n"
        . sprintf(
            Yii::t('gestion', 'Cualquier contenido que pueda haber en la sección «%s» '
            . 'será sobrescrito <b>para todos los estudios</b> de tipo %s.'),
            Yii::t('db', $seccion->titulo),
            $tipoEstudio->nombre
        )
        . '</div>',
    'closeButton' => false,
    'options' => ['class' => 'alert-danger'],
]) . "\n\n";

$form = ActiveForm::begin([
    'action' => Url::to(['informacion/guardar-en-masa']),
    'id' => 'informacion',
    'layout' => 'horizontal',
]);
?>

<input type="hidden" name="tipoEstudio_id" value="<?php echo $tipoEstudio->id; ?>">
<input type="hidden" name="seccion_id" value="<?php echo $seccion->id; ?>">

<?php
echo Summernote::widget([
    'id' => 'texto',
    'name' => 'texto',
    'value' => '',
    'clientOptions' => [
        'lang' => $locale,
        'placeholder' => Yii::t('cati', 'Introduzca sus comentarios'),
    ],
]) . "\n\n";

echo '<hr>';

echo Html::a(
    '<span class="glyphicon glyphicon-circle-arrow-left"></span> ' . Yii::t('cati', 'Volver atrás'),
    ['editar-infos-en-masa', 'tipoEstudio_id' => $tipoEstudio->id],
    [
        'id' => 'retroceder',
        'class' => 'btn btn-info',
    ]
) . " &nbsp; \n";

echo Html::a(
    '<span class="glyphicon glyphicon-check"></span> ' . Yii::t('cruds', 'Save'),
    ['', '#' => 'modalEditarEnMasa'],
    [
        'id' => 'save-informacion',
        'class' => 'btn btn-danger',
        'data-toggle' => 'modal',
    ]
);

?>
<!-- Diálogo modal -->
<div id="modalEditarEnMasa" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo Yii::t('gestion', '¿Editar en masa?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?php printf(
                    Yii::t(
                        'gestion',
                        '¿Seguro que desea modificar la sección «%s> de todos los estudios'
                        . ' de tipo %s?<br>'
                        . 'Los contenidos que pueda haber ahora serán sobrescritos irreversiblemente.'
                    ),
                    Yii::t('db', $seccion->titulo),
                    $tipoEstudio->nombre
                ); ?></p>
            </div>
            <div class="modal-footer">
                <?php
                echo Html::submitButton(
                    '<span class="glyphicon glyphicon-exclamation-sign"></span> '
                      . Yii::t('gestion', 'Guardar nuevo texto'),
                    [
                        'id' => 'save-informacion',
                        'class' => 'btn btn-danger',
                        'title' => Yii::t('gestion', 'Reemplazar los textos que haya actualmente por el nuevo.'),
                    ]
                );
                ?>
                <button type="button" class="btn btn-info" data-dismiss="modal">
                    <?php echo '<span class="glyphicon glyphicon-remove"></span> ' . Yii::t('gestion', 'Cancelar'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php
ActiveForm::end();
?>