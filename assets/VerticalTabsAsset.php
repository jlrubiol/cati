<?php
/**
 * @see http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 *
 * @since 2.0
 */
class VerticalTabsAsset extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap-vertical-tabs';
    public $js = [
        'index.js',
    ];
    public $css = [
        'bootstrap.vertical-tabs.min.css',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}
