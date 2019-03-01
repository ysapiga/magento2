<?php

namespace Sapiha\Import\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }

//    /**
//     * Check Permission.
//     *
//     * @return bool
//     */
//    protected function _isAllowed()
//    {
//        return $this->_authorization->isAllowed('Sapiha_Import::manage_import');
//    }
}