<?php

namespace Sapiha\Import\Model;

use Magento\Framework\File\Csv;
use Psr\Log\LoggerInterface;

class Importer
{

    /** @var array */
    protected $data;

    /** @var string  */
    public $filePAth;

    /** @var $array */
    protected $requiredFields;

    /** @var string  */
    protected $delimeter = ',';


    /**
     * Importer constructor.
     * @param Csv $csv
     * @param string $filePAth
     */
    public function __construct(
        Csv $csv,
        string $filePath,
        LoggerInterface $logger,
        string $delimeter = ','
    )
    {
        $this->csv = $csv;
        $this->filePAth = $filePath;
        $this->logger = $logger;
        $this->delimeter = $delimeter;
    }

    /**
     * Read file data
     *
     * @throws \Exception
     * @return void
     */
    public function readFile()
    {
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
        return $this->validateData($this->csv->getData($this->filePAth));
    }


    /**
     * Validate fields
     *
     * @return bool
     */
    protected function validateFields()
    {
        return $this->requiredFields
            ? array_intersect($this->getData()[0], $this->requiredFields) == $this->requiredFields
            : true;
    }

    /**
     * Validate File
     *
     * @param array $data
     * @return array
     */
    private function validateData(array $data)
    {
        $headCount = count($data[0]);
        $validData = [];
        foreach ($data as $rowNumb => $row) {
            if(count($row) !== $headCount) {
                $this->logger->notice("The row number $rowNumb skipped because of not valid data");
                continue;
            }


            $validData[] = $row;
        }

        return $validData;
    }


}