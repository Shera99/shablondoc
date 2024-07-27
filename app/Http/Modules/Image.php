<?php

namespace App\Http\Modules;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image as MakeImage;
use Intervention\Image\ImageManager;

class Image
{
    public static function save($image, $type): array
    {
        $basePath = 'public/images/';
        $datePath = date('Y/m/d');
        $path = $basePath . $type . '/' . $datePath . '/';

        try {
            if ($image->getSize() > 10 * 1024 * 1024) {
                throw new \Exception('File size exceeds the maximum limit of 10MB.');
            }

            if (!Storage::exists($path)) {
                Storage::makeDirectory($path);
            }

            $imageName = time().'.'.$image->getClientOriginalExtension();
//            $storedImagePath = $image->storeAs($path, $imageName);

//            $manager = new ImageManager(
//                new \Intervention\Image\Drivers\Gd\Driver()
//            );

//            $resizedImage = $manager->read($image->path());

            $resizedImage = MakeImage::read($image->getRealPath());

            if ($resizedImage->width() > 500) {
                $resizedImage->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

//            $encoded = $image->toJpg();

            $resizedImage->save(storage_path('app/' . $path . $imageName), 75);

            $imageUrl = Storage::url($path . $imageName);
//            $imageUrl = Storage::url($storedImagePath);

            return ['storedImagePath' => 'images/' . $type . '/' . $datePath . '/' . $imageName, 'imageUrl' => $imageUrl];
        } catch (\Exception $e) {
            return ['error' => $e->getCode(), 'message' => $e->getMessage()];
        }
    }
}
