<?php
use yii\helpers\HtmlPurifier;

?>

<div class="tab-content">
    <div class="tab-pane active" id="inicio">
        <?php
        foreach ($pag1 as $dato) {
            echo '<h2>'.Yii::t('db', $dato->seccion->titulo).'</h2>';
            echo '<div>'.HtmlPurifier::process($dato->texto).'</div>';
        }
        ?>
    </div>

    <div class="tab-pane" id="acceso">
        <?php
        foreach ($pag2 as $dato) {
            echo '<h2>'.Yii::t('db', $dato->seccion->titulo).'</h2>';
            echo '<div>'.HtmlPurifier::process($dato->texto).'</div>';
        }
        ?>
    </div>

    <div class="tab-pane" id="perfiles">
        <?php
        foreach ($pag3 as $dato) {
            echo '<h2>'.Yii::t('db', $dato->seccion->titulo).'</h2>';
            echo '<div>'.HtmlPurifier::process($dato->texto).'</div>';
        }
        ?>
    </div>

    <div class="tab-pane" id="queaprende">
        <?php
        foreach ($pag4 as $dato) {
            echo '<h2>'.Yii::t('db', $dato->seccion->titulo).'</h2>';
            echo '<div>'.HtmlPurifier::process($dato->texto).'</div>';
        }
        ?>
    </div>

    <div class="tab-pane" id="planes">
        <?php
        foreach ($pag5 as $dato) {
            echo '<h2>'.Yii::t('db', $dato->seccion->titulo).'</h2>';
            echo '<div>'.HtmlPurifier::process($dato->texto).'</div>';
        }
        ?>
    </div>

    <div class="tab-pane" id="apoyo">
        <?php
        foreach ($pag6 as $dato) {
            echo '<h2>'.Yii::t('db', $dato->seccion->titulo).'</h2>';
            echo '<div>'.HtmlPurifier::process($dato->texto).'</div>';
        }
        ?>
    </div>

    <div class="tab-pane" id="profesorado">
        <?php
        foreach ($pag7 as $dato) {
            echo '<h2>'.Yii::t('db', $dato->seccion->titulo).'</h2>';
            echo '<div>'.HtmlPurifier::process($dato->texto).'</div>';
        }
        ?>
    </div>
</div> <!-- tab-content -->
