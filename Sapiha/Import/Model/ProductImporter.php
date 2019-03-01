<?php

namespace Sapiha\Import\Model;

use Magento\Framework\File\Csv;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Model\Product\Type;

class ProductImporter extends Importer
{

    const DEFAULT_ATTRIBUTE_SET_ID = 4;

    /** @var array  */
    protected $requiredFields = ['sku', 'name', 'price'];

    /**
     * ProductImporter constructor.
     * @param Csv $csv
     * @param string $filePAth
     * @param ProductInterfaceFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(
        Csv $csv,
        string $filePAth = Decoder::TMP_IMPORT_PATH,
        ProductInterfaceFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry
    )
    {
        parent::__construct($csv, $filePAth);

        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
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
               $product->setData($this->prepareData($row));
               $product->setTypeId(Type::TYPE_SIMPLE);
               $product->setAttributeSetId(self::DEFAULT_ATTRIBUTE_SET_ID);
               try {
                   $this->productRepository->save($product);
               } catch (\Exception $e) {
                   Decoder::deleteFile();
               }
           }
        }
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
}