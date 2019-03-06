<?php

namespace Sapiha\Import\Block\Adminhtml\Product\Edit;

use Magento\ImportExport\Model\Import;
use Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('adminhtml/*/import'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );
        $fieldset = $form->addFieldset(
            'upload_file_fieldset',
            ['legend' => __('File to Import')]
        );
        $fieldset->addField(
            'import_strategy',
            'select',
            [
                'name' => 'import_strategy',
                'label' => __('Import Strategy'),
                'title' => __('Import_strategy'),
                'required' => true,
                'note' => __('If you chose delete strategy the products will be deleted by sku from file'),
                'values' => [
                    0 => __('Add Products'),
                    1 => __('Delete Products'),
                ]
            ]
        );
        $fieldset->addField(
            Import::FIELD_FIELD_SEPARATOR,
            'text',
            [
                'name' => Import::FIELD_FIELD_SEPARATOR,
                'label' => __('Field separator'),
                'title' => __('Field separator'),
                'required' => true,
                'value' => ',',
            ]
        );
        $fieldset->addField(
            Import::FIELD_NAME_SOURCE_FILE,
            'file',
            [
                'name' => Import::FIELD_NAME_SOURCE_FILE,
                'label' => __('Select File to Import'),
                'title' => __('Select File to Import'),
                'required' => true,
                'class' => 'input-file',
                'note' => __(
                    'File must be saved in UTF-8 encoding for proper import'
                ),
            ]
        );
        $fieldset->addField(
            'base64_file',
            'hidden',
            [
                'name' => 'base64_file',
            ]
        );
        $fieldset->addField(
            'import_type',
            'hidden',
            [
                'name' => 'import_type',
                'value' => 'product',
            ]
        );

        $fieldsets['upload'] = $fieldset;

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
