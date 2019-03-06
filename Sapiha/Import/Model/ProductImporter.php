<?php

namespace Sapiha\Import\Model;

use Magento\Framework\File\Csv;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Model\Product\Type;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class ProductImporter extends Importer
{

    const DEFAULT_ATTRIBUTE_SET_ID = 4;

    /** @var array  */
    protected $requiredFields = ['sku', 'name', 'price'];

    /** @var LoggerInterface */
    protected $logger;

    /**
     * ProductImporter constructor.
     * @param Csv $csv
     * @param string $filePAth
     * @param ProductInterfaceFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param StockRegistryInterface $stockRegistry
     * @param LoggerInterface $logger
     * @param string $delimeter
     */
    public function __construct(
        Csv $csv,
        string $filePath,
        ProductInterfaceFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        LoggerInterface $logger,
        string $delimeter = ',',
        CollectionFactory $categoryCollectionFactory
    )
    {
        parent::__construct($csv, $filePath, $logger,  $delimeter);

        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Create products
     *
     * @return void
     */
    public function createProducts()
    {
        if ($this->validateFields()) {
           foreach ($this->data as $rowNumb => $row) {
               if ($rowNumb == 0) {
                   continue;
               }
               $product = $this->productFactory->create();
               if (!$product->getIdBySku($row[0])) {
                   $product->setData($this->prepareData($row));
                   $product->setTypeId(Type::TYPE_SIMPLE);
                   $product->setAttributeSetId(self::DEFAULT_ATTRIBUTE_SET_ID);
                   $product->setCategoryIds($this->getCategoryIdsByName($row[3]));
                   try {
                       $this->productRepository->save($product);
                   } catch (\Exception $e) {
                       Decoder::deleteFile();
                   }
               }


           }
        }
    }

    /**
     * Update product stock
     *
     * @param $row
     */
    private function updateStock($row)
    {

    }

    /**
     * Delete products
     *
     * @return void
     */
    public function deleteProducts()
    {
        if ($this->validateFields()) {
            foreach ($this->data as $rowNumb => $row) {
                if ($rowNumb == 0) {
                    continue;
                }
                try {
                    $this->productRepository->deleteById($this->prepareData($row)['sku']);
                } catch (\Exception $e) {
                    Decoder::deleteFile();
                }
            }
        }
    }

    /**
     * Prepare data o save
     *
     * @param string $row
     * @return array
     */
    private function prepareData($row)
    {
        $data = [];
        $i = 0;

        foreach ($this->data[0] as $field) {
            $data[$field] = $row[$i];
            $i++;
        }

        return $data;
    }

    /**
     * Get Category ids by name
     *
     * @param string $name
     * @return $array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCategoryIdsByName($name)
    {
        $categoryIds = [];
        $collection = $this->categoryCollectionFactory->create()->addAttributeToFilter('name', $name);
        foreach ($collection as $item) {
            $categoryIds[] = $item->getId();
        }

        return $categoryIds;
    }
}