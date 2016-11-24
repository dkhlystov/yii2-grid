<?php

namespace dkhlystov\grid;

use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use dkhlystov\grid\assets\ArrayInputAsset;

class ArrayInput extends InputWidget
{

	public $itemClass;

	public $columns;

	public $tableOptions = ['class' => 'table table-condensed'];

	public $addButtonOptions = ['class' => 'btn btn-default'];

	public $addLabel = 'Add';

	public $removeLabel = 'Remove';

	private $_items;

	private $_columns;

	public function init()
	{
		parent::init();

		if ($this->itemClass === null)
			throw new InvalidConfigException('Property "itemClass" must be set.');

		if ($this->columns === null)
			throw new InvalidConfigException('Property "columns" must be set.');

		$this->registerClientScript();
	}

	public function run()
	{
		$this->prepareItems();
		$this->prepareColumns();

		$options = $this->options;
		Html::addCssClass($options, 'array-input');

		$dataProvider = new ArrayDataProvider([
			'allModels' => $this->_items,
			'pagination' => false,
		]);

		echo GridView::widget([
			'layout' => "{items}\n{summary}",
			'tableOptions' => $this->tableOptions,
			'options' => $options,
			'summary' => Html::button($this->addLabel, $this->addButtonOptions),
			'dataProvider' => $dataProvider,
			'showHeader' => false,
			'rowOptions' => function($model, $key, $index, $grid) {
				return $index == 0 ? ['class' => 'hidden'] : [];
			},
			'columns' => $this->_columns,
		]);
	}

	private function registerClientScript()
	{
		ArrayInputAsset::register($this->view);
	}

	private function prepareItems()
	{
		$attribute = $this->attribute;
		$items = $this->model->$attribute;
		if (!is_array($items))
			$items = [];

		$class = $this->itemClass;
		$items = array_merge([new $class], $items);

		$this->_items = $items;
	}

	private function prepareColumns()
	{
		$basename = Html::getInputName($this->model, $this->attribute);

		$columns = [];
		foreach ($this->columns as $column) {
			if (is_string($column))
				$column = ['attribute' => $column];

			$columns[] = array_merge([
				'class' => 'dkhlystov\grid\TextInputColumn',
				'attribute' => 'description',
				'basename' => $basename,
				'inputOptions' => function($model, $key, $index, $column) {
					return [
						'disabled' => $index == 0,
					];
				}
			], $column);
		}

		$columns[] = [
			'class' => 'yii\grid\ActionColumn',
			'options' => ['style' => 'width: 25px;'],
			'template' => '{remove}',
			'buttons' => [
				'remove' => function($url, $model, $key) {
					return Html::a('<span class="glyphicon glyphicon-remove"></span>', '#', [
						'class' => 'item-remove',
						'title' => $this->removeLabel,
					]);
				},
			],
		];

		$this->_columns = $columns;
	}

}
