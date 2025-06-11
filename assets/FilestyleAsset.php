<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle para embellecer los formularios con subida de ficheros.
 *
 * @see https://github.com/markusslima/bootstrap-filestyle.
 * @author Enrique Matías Sánchez <quique@unizar.es>
 */
class FilestyleAsset extends AssetBundle
{
    // public $basePath = '@webroot';
    // public $baseUrl = '@web';
    public $sourcePath = '@bower/bootstrap-filestyle/src';
    public $css = [
    ];
    public $js = [
        'bootstrap-filestyle.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
