<?php

namespace App\Http\Services;

use App\Http\Modules\FileHandler;
use App\Http\Modules\Image;
use App\Http\Requests\Api\Certification\CertificationSignatureCU;

class CertificationService
{
    /**
     * @throws \Exception
     */
    public function formatDataAndSaveImage(CertificationSignatureCU $request): array
    {
        $data = $request->only([
            'company_id', 'country_id', 'city_id', 'language_id',
            'certification_signature_type_id', 'user', 'view', 'certification_text'
        ]);

        if ($request->hasFile('file')) {
            $files = $request->file('file');

            if (!is_array($files)) {
                $files = [$files];
            }

            $image_save_result = [];

            foreach ($files as $file) {
                $image_save_result = FileHandler::save($file, 'cert');
            }

            $data['file'] = $image_save_result['storedFilePath'];
        }

        return $data;
    }
}
