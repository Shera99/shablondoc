<?php

namespace App\Http\Modules;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image as MakeImage;

class FileHandler
{
    /**
     * @throws \Exception
     */
    public static function save($file, $type): array
    {
        $basePath = 'public/images/';
        $datePath = date('Y/m/d');
        $path = $basePath . $type . '/' . $datePath . '/';

        try {
            if ($file->getSize() > 29 * 1024 * 1024) {
                throw new \Exception('File size exceeds the maximum limit of 29MB.');
            }

            if (!Storage::exists($path)) {
                Storage::makeDirectory($path);
            }

            $fileName = Str::random(10).time().'.'.$file->getClientOriginalExtension();

            if (in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
                $resizedImage = MakeImage::read($file->getRealPath());

                if ($resizedImage->width() > 500) {
                    $resizedImage->scale(500, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                $resizedImage->save(storage_path('app/' . $path . $fileName), 75);
            } elseif (in_array($file->getClientOriginalExtension(), ['pdf', 'doc', 'docx'])) {
                $file->storeAs($path, $fileName);
            } else {
                throw new \Exception('Unsupported file type.');
            }

            $fileUrl = Storage::url($path . $fileName);

            return ['storedFilePath' => 'images/' . $type . '/' . $datePath . '/' . $fileName, 'fileUrl' => $fileUrl];
        } catch (\Exception $e) {
            Log::error('FileHandler error: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}
