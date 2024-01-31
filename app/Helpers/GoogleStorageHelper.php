<?php

namespace App\Helpers;

use Storage;

class GoogleStorageHelper
{

    private $storage;

    public function __construct() 
    {
        $this->storage = Storage::disk("gcs");
    }

    public function storeFile($file, $folder = "folder") 
    {
        $storedFile = $this->storage->put($folder, $file);
        return $storedFile;
    }

    public function deleteFile($url) 
    {
        if($url && $this->storage->exists($url)) {
            $this->storage->delete($url);
        }
    }

    public function renderFileUrl($url)
    {      
        if($url) {
            if($this->storage->exists($url)) {
                return $this->storage->temporaryUrl($url, now()->addHours(1));   
            } else {
                return asset(theme()->getMediaUrlPath().'avatars/blank.png');
            }
        }

    }

    public function renderPdfUrl($url) 
    {
       if($url) {
            return $this->storage->temporaryUrl($url, now()->addHours(1));
        } 
    }

}