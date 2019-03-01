<?php

namespace Sapiha\Import\Block\Adminhtml;

use Magento\Framework\View\Element\Template;

class Import extends Template
{
    public function getProductActionUrl()
    {
        return $this->getUrl('custom_import/product/import' );
    }

    public function getCategoryActionUrl()
    {
        return $this->getUrl('custom_import/category/import' );
    }

}