<?php

namespace dkhlystov\grid\assets;

use yii\web\AssetBundle;

class MovingColumnAsset extends AssetBundle
{

    public $css = [
        'movingcolumn' . (YII_DEBUG ? '' : '.min') . '.css',
    ];

    public $js = [
        'movingcolumn' . (YII_DEBUG ? '' : '.min') . '.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . '/moving-column';
    }

}
