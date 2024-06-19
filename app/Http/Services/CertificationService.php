<?php

namespace App\Http\Services;

use App\Http\Modules\Image;
use App\Http\Requests\Api\Certification\CertificationSignatureCU;

class CertificationService
{
    public function formatDataAndSaveImage(CertificationSignatureCU $request): array
    {
        $data = $request->only([
            'company_id', 'country_id', 'city_id', 'language_id',
            'certification_signature_type_id', 'user', 'view', 'certification_text'
        ]);

        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $image_save_result = Image::save($image, 'cert');

            if (in_array('error', $image_save_result)) return $image_save_result;

            $data['file'] = $image_save_result['storedImagePath'];
        }

        return $data;
    }
}
