<?php

namespace dkhlystov\grid\assets;

use yii\web\AssetBundle;

class ArrayInputAsset extends AssetBundle
{

	public $js = [
		'array-input.js',
	];

	public $css = [
		'array-input.css',
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		$this->sourcePath = __DIR__ . '/array-input';
	}

}
