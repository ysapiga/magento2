<?php

namespace Sapiha\Import\Model;


class Decoder
{

    const TMP_IMPORT_PATH = 'var/custom_import/';

    /** @var string  */
    private $fileString;

    /** @var bool  */
    private $success;

    /** @var string  */
    protected $delimeter = ',';

    /** @var string */
    public static $fileName;

    /**
     * Decoder constructor.
     * @param string $fileString
     */
    public function __construct(string $fileString, string $delimeter = ',')
    {
        $this->fileString = $fileString;
        $this->delimeter = $delimeter;
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
            $data = explode($this->delimeter, $this->fileString);
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
            self::$fileName = time() . '.csv';
            file_put_contents(self::TMP_IMPORT_PATH .  self::$fileName, $content);
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
        if (!is_dir(self::TMP_IMPORT_PATH)) {
            mkdir(self::TMP_IMPORT_PATH);
        }
    }

    /**
     * Remove tmp file
     *
     * @return void
     */
    public static function deleteFile()
    {
        if (file_exists(self::TMP_IMPORT_PATH .  self::$fileName)) {
            unlink(self::TMP_IMPORT_PATH .  self::$fileName );
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

    /**
     * Get filePath
     *
     * @return string
     */
    public static function getFilePath()
    {
        return self::TMP_IMPORT_PATH . self::$fileName;
    }
}