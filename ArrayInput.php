<?php

namespace dkhlystov\grid;

use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use dkhlystov\grid\assets\ArrayInputAsset;

/**
 * Widget for array input field
 */
class ArrayInput extends InputWidget
{

	/**
	 * @var string the class of the every row in input array
	 */
	public $itemClass;

	/**
	 * @var string[]|array[] array of attributes or array of columns configs
	 * @see [[dkhlystov\grid\TextInputColumn]]
	 * @see [[yii\grid\GridView::columns]]
	 */
	public $columns;

	/**
	 * @var array HTML options for table
	 */
	public $tableOptions = ['class' => 'table table-condensed'];

	/**
	 * @var array HTML options for add button
	 */
	public $addButtonOptions = ['class' => 'btn btn-default'];

	/**
	 * @var string label for add button
	 */
	public $addLabel = 'Add';

	/**
	 * @var string label for remove link
	 */
	public $removeLabel = 'Remove';

	/**
	 * @var array models with empty template
	 */
	private $_items;

	/**
	 * @var array prepared columns with actions column
	 */
	private $_columns;

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		if ($this->itemClass === null)
			throw new InvalidConfigException('Property "itemClass" must be set.');

		if ($this->columns === null)
			throw new InvalidConfigException('Property "columns" must be set.');

		$this->registerClientScript();
	}

	/**
	 * @inheritdoc
	 */
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

		echo Html::activeHiddenInput($this->model, $this->attribute, ['value' => '']);

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

	/**
	 * Register CSS classes and js
	 * @return void
	 */
	private function registerClientScript()
	{
		ArrayInputAsset::register($this->view);
	}

	/**
	 * Prepares items for render in table
	 * @return void
	 */
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

	/**
	 * Prepares columns for table
	 * @return void
	 */
	private function prepareColumns()
	{
		$basename = Html::getInputName($this->model, $this->attribute);

		$columns = [];
		foreach ($this->columns as $column) {
			if (is_string($column))
				$column = ['attribute' => $column];

			$columns[] = array_merge([
				'class' => isset($column['items']) ? 'dkhlystov\grid\DropdownInputColumn' : 'dkhlystov\grid\TextInputColumn',
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
