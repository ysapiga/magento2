<?php

namespace Sapiha\Import\Model;

use Magento\Catalog\Model\Category;
use Magento\Framework\File\Csv;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class CategoryImporter extends Importer
{

    /** @var array  */
    protected $requiredFields = ['name', 'is_active', 'parent'];

    /**
     * CategoryImporter constructor.
     * @param Csv $csv
     * @param string $filePAth
     * @param CategoryFactory $categoryFactory
     * @param CategoryRepository $categoryRepository
     * @param ProductRepositoryInterface $productRepository
     * @param CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        Csv $csv,
        string $filePAth = Decoder::TMP_IMPORT_PATH,
        CategoryFactory $categoryFactory,
        CategoryRepository $categoryRepository,
        ProductRepositoryInterface $productRepository,
        CollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager

    )
    {
        parent::__construct($csv, $filePAth);

        $this->categoryFactory = $categoryFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Create category
     *
     * @return void
     */
    public function createCategories()
    {
            if ($this->validateFields()) {
            foreach ($this->data as $rowNumb => $row) {
                if ($rowNumb == 0) {
                    continue;
                }

                $category = $this->categoryFactory->create();
                $category->setData($this->prepareData($row));
                try {
                    $this->categoryRepository->save($category);
                } catch (\Exception $e) {
                    Decoder::deleteFile();
                }
            }
        }
    }

    /**
     * Delete categories
     *
     * @return void
     */
    public function deleteCategory(bool $removeProducts = false)
    {
        if ($this->validateFields()) {
            foreach ($this->data as $rowNumb => $row) {
                if ($rowNumb == 0) {
                    continue;
                }
                try {
                    $categoryCollection = $this->getCategoryCollectionByName($this->prepareData($row)['name']);
                    if (count($categoryCollection->getItems()) > 0) {
                        foreach ($categoryCollection as $category) {
                            $this->categoryRepository->delete($category);
                            if ($removeProducts) {
                                $this->deleteAssignedProducts($category);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Decoder::deleteFile();
                }
            }
        }
    }

    /**
     * Delete all products assigned to the category
     *
     * @param Category $category
     * @return void
     */
    public function deleteAssignedProducts($category)
    {
        $productCollection = $category->getProductCollection();
        if ($productCollection->getItems() > 0) {
            foreach ($productCollection as $product) {
                try {
                    $this->productRepository->delete($product);
                } catch (\Exception $e) {
                    /** ToDo log an Exception */
                    $e->getMessage();
                }
            }
        }
    }

    /**
     * Get Category collection by name
     *
     * @param string $name
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCategoryCollectionByName($name)
    {
        $collection = $this->categoryCollectionFactory->create()->addAttributeToFilter('name', $name);

        return $collection;

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
        $data['parent'] = $data['parent'] ?: $this->getRootCategoryId();


        return $data;
    }

    /**
     * Get root category id
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getRootCategoryId()
    {
        return $this->storeManager->getStore()->getRootCategoryId();
    }
}
