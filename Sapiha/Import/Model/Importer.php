<?php

namespace Sapiha\Import\Model;

use Magento\Framework\File\Csv;

class Importer
{

    /** @var array */
    protected $data;

    /** @var string  */
    public $filePAth;

    /** @var $array */
    protected $requiredFields;

    /**
     * Importer constructor.
     * @param Csv $csv
     * @param string $filePAth
     */
    public function __construct(
        Csv $csv,
        string $filePAth = Decoder::TMP_IMPORT_PATH
    )
    {
        $this->csv = $csv;
        $this->filePAth = $filePAth;
        $this->data = $this->getDataFromFile();
    }

    /**
     * Return importer data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get data from csv file
     *
     * @return array
     * @throws \Exception
     */
    private function getDataFromFile()
    {
        return $this->csv->getData($this->filePAth);
    }


    /**
     * Validate fields
     *
     * @return bool
     */
    protected function validateFields()
    {
        /** Todo (має робити даже якшо в наследніка не переопреділені реквайред поля) */
        return count(array_diff($this->data[0], $this->requiredFields)) == 0;
    }


}