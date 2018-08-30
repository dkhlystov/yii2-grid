<?php

namespace dkhlystov\grid;

use Closure;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 * Active boolean input column for [[GridView]].
 */
class BooleanInputColumn extends TextInputColumn
{

    /**
     * @var array The option data items.
     * @see [[yii\helpers\Html::dropDownList]]
     */
    public $items;

    /**
     * @inheritdoc
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

        if (!array_key_exists('class', $options)) {
            // $options['class'] = 'form-control';
        }

        if (!array_key_exists('label', $options)) {
            $options['label'] = $model->getAttributeLabel($attribute);
        }

        if (($readOnlyAttribute = $this->readOnlyAttribute) !== null && $model->$readOnlyAttribute) {
            $options['disabled'] = true;
        }

        return Html::checkbox($name, $value, $options);
    }

}
