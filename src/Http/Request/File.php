<?php

namespace Dez\Http\Request;

class File {

    const SIZE_KILOBYTES = 1;

    const SIZE_MEGABYTES = 2;

    const SIZE_GIGABYTES = 4;

    protected static $errorDescriptions = [
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
    ];

    protected $name;

    protected $temporaryName;

    protected $type;

    protected $size;

    protected $error;

    protected $key;

    protected $extension;

    /**
     * File constructor.
     * @param array $file
     */
    public function __construct(array $file = [])
    {
        $this->name = isset($file['name']) ? $file['name'] : null;
        $this->temporaryName = isset($file['tmp_name']) ? $file['tmp_name'] : null;
        $this->type = isset($file['type']) ? $file['type'] : null;
        $this->size = isset($file['size']) ? $file['size'] : 0;
        $this->error = isset($file['error']) ? $file['error'] : 0;
        $this->key = isset($file['key']) ? $file['key'] : null;
        $this->extension = pathinfo($this->name, PATHINFO_EXTENSION);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTemporaryName()
    {
        return $this->temporaryName;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getRealMimeType()
    {
        $info = finfo_open(FILEINFO_MIME);

        $mime = finfo_file($info, $this->getTemporaryName());
        finfo_close($info);

        return $mime;
    }

    /**
     * @param int $scale
     * @return float
     */
    public function getSize($scale = 0)
    {
        $size = $this->size;

        if($scale === static::SIZE_KILOBYTES) {
            $size = round($size / 1024, 4);
        }

        if($scale === static::SIZE_MEGABYTES) {
            $size = round($size / (1024 * 1024), 4);
        }

        if($scale === static::SIZE_GIGABYTES) {
            $size = round($size / (1024 * 1024 * 1024), 4);
        }

        return $size;
    }

    /**
     * @return integer
     */
    public function getErrorCode()
    {
        return $this->error;
    }

    /**
     * @return integer
     */
    public function getErrorDescription()
    {
        return isset(static::$errorDescriptions[$this->error]) ? static::$errorDescriptions[$this->error] : 'Unknown error';
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return bool
     */
    public function isFailed()
    {
        return $this->getErrorCode() !== UPLOAD_ERR_OK;
    }

    /**
     * @return boolean
     */
    public function isUploaded()
    {
        return file_exists($this->getTemporaryName()) && is_uploaded_file($this->getTemporaryName());
    }

    /**
     * @param null $destination
     * @return boolean
     */
    public function moveTo($destination = null)
    {
        return move_uploaded_file($this->getTemporaryName(), $destination);
    }

}