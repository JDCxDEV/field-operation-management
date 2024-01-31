<?php

namespace App\Helpers;

use Illuminate\Support\Str;

use Image;
use Storage;

class ImageHelper
{
    private $width = 1000;
    private $height = 1000;

    private $compressWidth = 600;
    private $compressHeight = 600;    

    public function compress($image, $folder) 
    {
        $imageName = Str::random(60) . '.'. $image->getClientOriginalExtension();
        $img = Image::make($image);
        
        $storePath = $folder .'/'. $imageName;

        if($img->height() >= $this->height || $img->width() >= $this->width) {
            $height = $img->height() < $this->compressHeight ? $img->height() : $this->compressHeight;
            $width = $img->width() < $this->compressWidth ? $img->width() : $this->compressWidth;
            $img->resize($height, $width, function ($constraint) {
                $constraint->aspectRatio();
            })->encode($image->getClientOriginalExtension());
            $file = Storage::disk('gcs')->put($storePath, $img->getEncoded());
            return $storePath;
        } else {
            return Storage::disk('gcs')->put($folder, $image);
        }
    }

}