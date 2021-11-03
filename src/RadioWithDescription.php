<?php

declare(strict_types=1);

namespace atkuifields;

use Atk4\Data\Model;
use atk4\data\Persistence\Static_;
use Atk4\Ui\Form;

class RadioWithDescription extends Form\Control\Input
{
    public $defaultTemplate = 'radio_with_description.html';
    public $descriptionField;
    public $selectedId;
    public $descriptionArray = [];
    public $values;
    protected $_tRow;

    protected function init(): void
    {
        parent::init();
        $this->_tRow = $this->template->cloneRegion('Row');
        $this->template->del('Row');
        $this->_tRow->set('_name', $this->short_name);
    }

    /**
     * Renders view.
     */
    protected function renderView(): void
    {
        if (!$this->model) {
            $p = new Static_($this->values);
            $this->setModel(new Model($p));
        }
        $value = $this->field ? $this->field->get() : $this->selectedId;

        if ($this->disabled) {
            $this->addClass('disabled');
        }

        foreach ($this->model as $record) {
            $this->_appendRow($record, $value);
        }
    }

    protected function _appendRow(Model $record, $value)
    {
        if ($this->readonly) {
            $this->_tRow->set('disabled', $value != $record->id ? 'disabled="disabled"' : '');
        } elseif ($this->disabled) {
            $this->_tRow->set('disabled', 'disabled="disabled"');
        }

        if ($this->descriptionField) {
            $this->_tRow->set('description', $record->get($this->descriptionField));
        } elseif ($this->descriptionArray) {
            $this->_tRow->set('description', $this->descriptionArray[$record->get($record->id_field)]);
        }
        if ($record->hasField('icon')) {
            $this->_tRow->set('icon', $record->get('icon'));
        }
        if ($this->model->hasField('html')) {
            $this->_tRow->setHTML('extra_html', $record->get('html'));
        }

        if ($record->get($record->id_field) == $value) {
            $this->_tRow->set('checked', 'checked="checked"');
        } else {
            $this->_tRow->set('checked', '');
        }
        $this->_tRow->set('id', $record->getId());
        $this->_tRow->set('name', $record->get($record->title_field));

        $this->template->appendHTML('Row', $this->_tRow->render());
    }
}
