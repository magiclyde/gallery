<?php

namespace App\Service;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use TusPhp\Tus\Client as TusClient;

class FileManager
{
    private $path;

    // a chunk of 2MB
    const CHUNK_SIZE = 2000000;

    public function __construct($path)
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException(sprintf('Target directory %s doesn\'t exist', $path));
        }

        $this->path = $path;
    }

    public function getUploadsDirectory()
    {
        return $this->path;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    } 
    
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function upload(UploadedFile $file, $filename)
    {
        $fileSize = $file->getSize();

        if ($fileSize < self::CHUNK_SIZE) {
            $file = $file->move($this->getUploadsDirectory(), $filename);

            return $fileSize;
        }

        // ------ use tus service ------ >

        $filePathName = $file->getPathname();

        $client = new TusClient($this->baseUrl);
        $client->setApiPath('/tus');// tus server endpoint.

        $uploadKey = md5($filePathName);
        $client->setKey($uploadKey)->file($filePathName, $filename);

        // Create and upload a chunk of CHUNK_SIZE
        $bytesUploaded = $client->upload(self::CHUNK_SIZE);

        // Resume, $bytesUploaded += CHUNK_SIZE
        while ($bytesUploaded < $fileSize) {
            $bytesUploaded = $client->upload(self::CHUNK_SIZE);
        }

        return $bytesUploaded;
    }

    public function getFilePath($filename)
    {
        return $this->getUploadsDirectory() . DIRECTORY_SEPARATOR . $filename;
    }

    public function getPlaceholderImagePath()
    {
        return $this->getUploadsDirectory() . '/../placeholder.jpg';
    }

}
