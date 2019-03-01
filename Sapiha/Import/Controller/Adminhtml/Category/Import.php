<?php

namespace Sapiha\Import\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Sapiha\Import\Model\DecoderFactory;
use Sapiha\Import\Model\CategoryImporterFactory;

class Import extends Action
{

    /**
     * Import constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param DecoderFactory $decoderFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        DecoderFactory $decoderFactory,
        CategoryImporterFactory $categoryImporterFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->decoderFactory = $decoderFactory;
        $this->categoryImporterFactory = $categoryImporterFactory;

        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $fileString = $this->getRequest()->getParam('fileString');

        if ($fileString) {
            $fileDecoder = $this->decoderFactory->create(['fileString' => $fileString ]);
            /** @var $prodImporter */
            $categoryImporter = $this->categoryImporterFactory->create();

            if($this->getRequest()->getParam('import_strategy')) {
                $categoryImporter->deleteCategory($this->getRequest()->getParam('products_strategy'));
            } else {
                $categoryImporter->createCategories();
            }

            $fileDecoder->deleteFile();



        } else {
            $result->setData(['error'=> 1]);
        }
        return $result->setData([
            'success' => 1
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sapiha_Import::category_import');
    }

}