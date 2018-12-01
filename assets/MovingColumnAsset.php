<?php

namespace dkhlystov\grid\assets;

use yii\web\AssetBundle;

class MovingColumnAsset extends AssetBundle
{

    public $css = [
        'movingcolumn.css',
    ];

    public $js = [
        'movingcolumn.js',
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
