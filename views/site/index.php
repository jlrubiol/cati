<?php

/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\jui\AutoComplete;

$this->title = Yii::t('cati', 'Inicio').' | '.Yii::t('db', Yii::$app->name);

$this->registerMetaTag([
    'name' => 'description',
    'content' => Yii::t(
        'cati',
        'Titulaciones ofrecidas por la Universidad de Zaragoza: Estudios de Grado, Máster y Programas de Doctorado'
    ),
]);

$this->registerCssFile('@web/css/mainpage.css', ['depends' => 'app\assets\AppAsset']);
?>

<div class="site-index">

    <div class="jumbotron">
        <span class="icon-graduation-cap2"></span>
        <h1><?php echo Yii::t(
            'cati',
            'Oferta de <strong>estudios</strong><br><strong>oficiales</strong> universitarios'
        ); ?></h1>
        <span class="icon-stars" style="font-size: 2.4rem;"></span>
    </div>

    <form id="search-studies" action="<?php echo Url::to('estudio/ver'); ?>">
    <?php
    echo AutoComplete::widget([
        'id' => 'predictive-search',
        'clientEvents' => [
            'select' => "function (event, ui) {
                // event.preventDefault();
                $('#predictive-search').val(ui.item.label); // display the selected text
                $('#estudio-id').val(ui.item.value); // save selected id to hidden input
                $('#search-studies').submit();
                return false;
            }",
        ],
        'clientOptions' => [
            'source' => $estudios,
            'autoFill' => true,
            'minLength' => '1',
        ],
        'options' => [
            'class' => 'form-control',
            'placeholder' => Yii::t('cati', 'Buscar estudio...'),
            'aria-label' => Yii::t('cati', 'Nombre del estudio a buscar'),
        ],
    ])."\n";
    ?>
    <input type="hidden" id="estudio-id" name="id">
    <input type="submit" id="predictive-search-submit" value='Buscar' class="form-submit">
    </form>

    <div class="body-content">

        <div class="view-header">
            <ul>
                <li><?php echo Yii::t('cati', 'Listado alfabético'); ?>:
                    <a href="<?php echo Url::to(['estudio/lista', 'tipo_id' => 5]); ?>">
                        <?php echo Yii::t('cati', 'Grados'); ?>
                    </a> |
                    <a href="<?php echo Url::to(['estudio/lista', 'tipo_id' => 6]); ?>">
                        <?php echo Yii::t('cati', 'Másteres universitarios'); ?>
                    </a> |
                    <a href="<?php echo Url::to(['estudio/lista', 'tipo_id' => 7]); ?>">
                        <?php echo Yii::t('cati', 'Doctorado'); ?>
                    </a>
                </li>
                <li><?php echo Yii::t('cati', 'Listado por ramas de conocimiento'); ?>:
                    <a href="<?php echo Url::to(['estudio/lista-ramas', 'tipo_id' => 5]); ?>">
                        <?php echo Yii::t('cati', 'Grados'); ?>
                    </a> |
                    <a href="<?php echo Url::to(['estudio/lista-ramas', 'tipo_id' => 6]); ?>">
                        <?php echo Yii::t('cati', 'Másteres universitarios'); ?>
                    </a> |
                    <a href="<?php echo Url::to(['estudio/lista-ramas', 'tipo_id' => 7]); ?>">
                        <?php echo Yii::t('cati', 'Doctorado'); ?>
                    </a>
                </li>
            </ul>
        </div>

        <div class="row">

            <div class="col-md-2 col-md-offset-1">
                <a href="<?php echo Url::to(['estudio/lista-rama', 'rama_id' => 'H']); ?>">
                    <span class="icon-r-book"></span><br>
                    <span class="rama"><?php echo Yii::t('cati', 'Artes y humanidades'); ?></span>
                </a>
            </div>
            <div class="col-md-2">
                <a href="<?php echo Url::to(['estudio/lista-rama', 'rama_id' => 'X']); ?>">
                    <span class="icon-r-flask"></span><br>
                    <span class="rama"><?php echo Yii::t('cati', 'Ciencias'); ?></span>
                </a>
            </div>
            <div class="col-md-2">
                <a href="<?php echo Url::to(['estudio/lista-rama', 'rama_id' => 'S']); ?>">
                    <span class="icon-r-user-md"></span><br>
                    <span class="rama"><?php echo Yii::t('cati', 'Ciencias de la salud'); ?></span>
                </a>
            </div>
            <div class="col-md-2">
                <a href="<?php echo Url::to(['estudio/lista-rama', 'rama_id' => 'J']); ?>">
                    <span class="icon-r-group"></span><br>
                    <span class="rama"><?php echo Yii::t('cati', 'Ciencias sociales y jurídicas'); ?></span>
                </a>
            </div>
            <div class="col-md-2">
                <a href="<?php echo Url::to(['estudio/lista-rama', 'rama_id' => 'T']); ?>">
                    <span class="icon-r-cogs"></span><br>
                    <span class="rama"><?php echo Yii::t('cati', 'Ingeniería y arquitectura'); ?></span>
                </a>
            </div>

        </div>

    </div>
</div>
