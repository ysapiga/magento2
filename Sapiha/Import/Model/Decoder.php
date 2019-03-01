<?php

namespace Sapiha\Import\Model;


class Decoder
{
    /** Todo (файл має мати унікальне значееня) */
    const TMP_IMPORT_DIR = 'var/custom_import/';

    const TMP_FILE_NAME = 'custom_import.csv';

    const TMP_IMPORT_PATH = 'var/custom_import/custom_import.csv';

    /** @var string  */
    private $fileString;

    /** @var bool  */
    private $success;

    /**
     * Decoder constructor.
     * @param string $fileString
     */
    public function __construct(string $fileString)
    {
        $this->fileString = $fileString;
        $this->success = $this->saveFile();
    }

    /**
     * Decode file
     *
     * @return bool|string
     */
    private function decode()
    {
        $result = false;

        if(is_string($this->fileString)) {
            $data = explode(',', $this->fileString);
            $result = base64_decode($data[1]);
        }

        return $result;

    }

    /**
     * Save file
     *
     * @return bool
     */
    public function saveFile()
    {
        $result = false;
        $content = $this->decode();
        if ($content) {
            $this->createTmpDirectory();
            file_put_contents(self::TMP_IMPORT_PATH, $content);
            $this->success = true;
            $result = true;
        }

        return $result;
    }

    /**
     * Create tmp directory if it doesn't exist
     *
     * @return void
     */
    private function createTmpDirectory()
    {
        if (!is_dir(self::TMP_IMPORT_DIR)) {
            mkdir(self::TMP_IMPORT_DIR);
        }
    }

    /**
     * Remove tmp file
     * ToDo (файл має мати унікальне значення)
     *
     * @return void
     */
    public static function deleteFile()
    {
        if (file_exists(self::TMP_IMPORT_PATH)) {
            unlink(self::TMP_IMPORT_PATH);
        }
    }

    /**
     * Return success status
     *
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }
}