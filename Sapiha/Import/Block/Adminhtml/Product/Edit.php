<?php

namespace Sapiha\Import\Block\Adminhtml\Product;
use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
        $this->buttonList->update('save', 'label', __('Run Import'));
        $this->buttonList->update('save', 'id', 'runImport');
        $this->buttonList->update('save', 'data_attribute', '');

        //$this->_objectId = 'import_id';
        $this->_blockGroup = 'Sapiha_Import';
        $this->_controller = 'adminhtml_product';
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Custom Import');
    }
}