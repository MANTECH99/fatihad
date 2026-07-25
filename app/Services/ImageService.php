<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class ImageService
{
    public function uploadAndOptimize($image, $directory = 'uploads', $width = 800, $quality = 80)
    {
        $filename = uniqid() . '_' . time() . '.webp';
        $path = $directory . '/' . $filename;

        $img = Image::make($image);

        if ($img->width() > $width) {
            $img->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        $img->encode('webp', $quality);

        Storage::disk('public')->put($path, $img);

        return $path;
    }

    public function uploadMultiple($images, $directory = 'uploads', $width = 800)
    {
        $paths = [];
        foreach ($images as $image) {
            $paths[] = $this->uploadAndOptimize($image, $directory, $width);
        }
        return $paths;
    }

    public function delete($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
