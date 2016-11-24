<?php

namespace dkhlystov\grid;

use Closure;
use yii\base\InvalidConfigException;
use yii\grid\Column;
use yii\helpers\Html;

/**
 * Active text input column for [[GridView]].
 */
class TextInputColumn extends Column
{

	/**
	 * @var string the attribute name associated with this column
	 */
	public $attribute;

	/**
	 * @var string then base name of input. If not specified, the result of [[\yii\base\Model::formName()]] will be used.
	 */
	public $basename;

	/**
	 * @var array|\Closure the HTML attributes for the input tag.
	 */
	public $inputOptions = [];

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		if ($this->attribute === null)
			throw new InvalidConfigException('Property "attribute" must be set.');
	}

	/**
	 * Renders a input
	 * @param mixed $model the data model being rendered
	 * @param mixed $key the key associated with the data model
	 * @param int $index the zero-based index of the data item among the item array returned by [[GridView::dataProvider]].
	 * @return string the rendering result
	 */
	protected function renderInput($model, $key, $index)
	{
		$attribute = $this->attribute;

		$name = $this->getInputName($model, $index);

		$value = $model->$attribute;

		if ($this->inputOptions instanceof Closure) {
			$options = call_user_func($this->inputOptions, $model, $key, $index, $this);
		} else {
			$options = $this->inputOptions;
		}

		if (!array_key_exists('class', $options))
			$options['class'] = 'form-control';

		if (!array_key_exists('placeholder', $options))
			$options['placeholder'] = $model->getAttributeLabel($attribute);

		return Html::textInput($name, $value, $options);
	}

	/**
	 * Gets a model input name for specified index
	 * @param mixed $model the data model being rendered
	 * @param int $index the zero-based index of the data item among the item array returned by [[GridView::dataProvider]].
	 * @return string
	 */
	protected function getInputName($model, $index)
	{
		if ($this->basename === null) {
			$name = $model->formName();
		} else {
			$name = $this->basename;
		}

		return $name . "[{$index}][{$this->attribute}]";
	}

	/**
	 * @inheritdoc
	 */
	public function renderDataCell($model, $key, $index)
	{
		if ($this->contentOptions instanceof Closure) {
			$options = call_user_func($this->contentOptions, $model, $key, $index, $this);
		} else {
			$options = $this->contentOptions;
		}

		$error = $model->getFirstError($this->attribute);
		if ($error !== null) {
			$options['title'] = $error;
			Html::addCssClass($options, 'has-error');
		}

		return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
	}

	/**
	 * @inheritdoc
	 */
	protected function renderDataCellContent($model, $key, $index)
	{
		if ($this->content === null) {
			return $this->renderInput($model, $key, $index);
		} else {
			return parent::renderDataCellContent($model, $key, $index);
		}
	}

}
