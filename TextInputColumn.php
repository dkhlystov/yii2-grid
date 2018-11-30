<?php

namespace dkhlystov\grid;

use Closure;
use yii\base\Model;
use yii\base\InvalidConfigException;
use yii\grid\Column;
use yii\helpers\ArrayHelper;
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
     * @var string then base name of input. If not specified, the result of [[\yii\base\Model::formName()]] will be used
     */
    public $basename;

    /**
     * @var string attribute label
     */
    public $label;

    /**
     * @var array|Closure the HTML attributes for the input tag
     */
    public $inputOptions = [];

    /**
     * @var string|null check that item is read-only
     */
    public $readOnlyAttribute;

    /**
     * @var string|Closure cell content template
     */
    public $template = '{input}';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->attribute === null) {
            throw new InvalidConfigException('Property "attribute" must be set.');
        }
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

        $value = ArrayHelper::getValue($model, $attribute);

        if ($this->inputOptions instanceof Closure) {
            $options = call_user_func($this->inputOptions, $model, $key, $index, $this);
        } else {
            $options = $this->inputOptions;
        }

        if (!array_key_exists('class', $options)) {
            $options['class'] = 'form-control';
        }

        if (!array_key_exists('placeholder', $options)) {
            $options['placeholder'] = $this->label === null ? $model->getAttributeLabel($attribute) : $this->label;
        }

        if (($readOnlyAttribute = $this->readOnlyAttribute) !== null && ArrayHelper::getValue($model, $readOnlyAttribute)) {
            $options['disabled'] = true;
        }

        return Html::textInput($name, $value, $options);
    }

    /**
     * Gets a model input name for specified index
     * @param mixed $model the data model being rendered
     * @param int $index the zero-based index of the data item among the item array returned by [[GridView::dataProvider]].
     * @return string
     */
    public function getInputName($model, $index, $attribute = null)
    {
        if ($attribute === null) {
            $attribute = $this->attribute;
        }

        if ($this->basename === null) {
            $name = Html::getInputName($model, $attribute);
        } else {
            $name = $this->basename . "[{$index}][{$attribute}]";
        }

        return $name;
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

        if ($model instanceof Model) {
            $error = $model->getFirstError($this->attribute);
            if ($error !== null) {
                $options['title'] = $error;
                Html::addCssClass($options, 'has-error');
            }
        }

        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->content === null) {
            $parts = [
                '{input}' => $this->renderInput($model, $key, $index),
            ];

            if ($this->template instanceof Closure) {
                $template = call_user_func($this->template, $model, $key, $index, $this);
            } else {
                $template = $this->template;
            }

            return strtr($template, $parts);
        } else {
            return parent::renderDataCellContent($model, $key, $index);
        }
    }

}
