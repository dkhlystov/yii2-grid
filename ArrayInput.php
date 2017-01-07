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
	 * @var string|null
	 */
	public $readOnlyAttribute;

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

		$dataProvider = new ArrayDataProvider([
			'allModels' => $this->_items,
			'pagination' => false,
		]);

		$hidden = Html::activeHiddenInput($this->model, $this->attribute, ['value' => '']);
		$button = Html::button($this->addLabel, $this->addButtonOptions);

		$grid = GridView::begin([
			'layout' => $hidden . "{items}" . $button,
			'tableOptions' => $this->tableOptions,
			'dataProvider' => $dataProvider,
			'showHeader' => false,
			'columns' => $this->_columns,
		]);

		$options = $this->options;
		Html::addCssClass($options, 'array-input');

		$class = $this->itemClass;
		$options['data-array-input-template'] = $grid->renderTableRow(new $class, 0, 0);

		$grid->options = $options;

		GridView::end();
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

		$this->_items = $items;
	}

	/**
	 * Prepares columns for table
	 * @return void
	 */
	private function prepareColumns()
	{
		$basename = Html::getInputName($this->model, $this->attribute);
		$readOnlyAttribute = $this->readOnlyAttribute;

		$columns = [];
		foreach ($this->columns as $column) {
			if (is_string($column))
				$column = ['attribute' => $column];

			if (empty($column['class'])) {
				$column = array_merge([
					'class' => isset($column['items']) ? 'dkhlystov\grid\DropdownInputColumn' : 'dkhlystov\grid\TextInputColumn',
					'basename' => $basename,
					'readOnlyAttribute' => $readOnlyAttribute,
				], $column);
			}

			$columns[] = $column;
		}

		$columns[] = [
			'class' => 'yii\grid\ActionColumn',
			'options' => ['style' => 'width: 25px;'],
			'template' => '{remove}',
			'buttons' => [
				'remove' => function($url, $model, $key) use ($readOnlyAttribute) {
					$readOnly = false;
					if ($readOnlyAttribute !== null)
						$readOnly = $model->$readOnlyAttribute;

					if ($readOnly)
						return '';

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
