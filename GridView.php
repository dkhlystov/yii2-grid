<?php

namespace dkhlystov\grid;

use yii\helpers\Html;
use dkhlystov\grid\assets\GridViewAsset;

/**
 * Horizontal scrolling support for mobile devices
 */
class GridView extends \yii\grid\GridView
{

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();
		GridViewAsset::register($this->view);
	}

	/**
	 * @inheritdoc
	 */
	public function renderItems()
	{
		return Html::tag('div', parent::renderItems(), ['class' => 'grid-view-items']);
	}

}
