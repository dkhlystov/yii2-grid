<?php

namespace dkhlystov\grid;

use Closure;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 * Active drop-down list input column for [[GridView]].
 */
class DropdownInputColumn extends TextInputColumn
{

    /**
     * @var array The option data items.
     * @see [[yii\helpers\Html::dropDownList]]
     */
    public $items;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->items === null) {
            throw new InvalidConfigException('Property "items" must be set.');
        }
    }

    /**
     * @inheritdoc
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
            $options['placeholder'] = $model->getAttributeLabel($attribute);
        }

        if (($readOnlyAttribute = $this->readOnlyAttribute) !== null && ArrayHelper::getValue($model, $readOnlyAttribute)) {
            $options['disabled'] = true;
        }

        return Html::dropDownList($name, $value, $this->items, $options);
    }

}
