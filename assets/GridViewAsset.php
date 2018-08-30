<?php

namespace dkhlystov\grid\assets;

use yii\web\AssetBundle;

class GridViewAsset extends AssetBundle
{

    public $css = [
        'gridview' . (YII_DEBUG ? '' : '.min') . '.css',
    ];

    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . '/grid-view';
    }

}
