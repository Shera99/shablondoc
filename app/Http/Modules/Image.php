<?php

namespace App\Http\Modules;

use Illuminate\Support\Facades\Storage;

class Image
{
    public static function save($image): array
    {
        $path = 'images/';

        try {
            if ($image->getSize() > 10 * 1024 * 1024) {
                throw new \Exception('File size exceeds the maximum limit of 10MB.');
            }

            $imageName = time().'.'.$image->getClientOriginalExtension();
            $storedImagePath = $image->storeAs($path, $imageName);
            $imageUrl = Storage::url($storedImagePath);

            return ['storedImagePath' => $storedImagePath, 'imageUrl' => $imageUrl];
        } catch (\Exception $e) {
            return ['error' => $e->getCode(), 'message' => $e->getMessage()];
        }
    }
}
