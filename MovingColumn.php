<?php

namespace dkhlystov\grid;

use Closure;
use yii\grid\Column;
use yii\helpers\Html;
use dkhlystov\grid\assets\MovingColumnAsset;

/**
 * Moving column for [[GridView]].
 */
class MovingColumn extends Column
{

    /**
     * @inheritdoc
     */
    public $contentOptions = ['class' => 'moving-column'];

    /**
     * @var string|null check that item can moving
     */
    public $movingDisabledAttribute;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        MovingColumnAsset::register($this->grid->view);
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

        if (($movingDisabledAttribute = $this->movingDisabledAttribute) !== null && $model->$movingDisabledAttribute) {
            $options['data-moving-disabled'] = true;
        }

        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->content === null) {
            if (($movingDisabledAttribute = $this->movingDisabledAttribute) !== null && $model->$movingDisabledAttribute) {
                return '';
            } else {
                return Html::tag('span', '', ['class' => 'moving-handle glyphicon glyphicon-option-vertical']);
            }
        } else {
            return parent::renderDataCellContent($model, $key, $index);
        }
    }

}
