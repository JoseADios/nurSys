<?php

// app/Services/FirmService.php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FirmService
{
    public function createImag($stringSign, $fieldValue)
    {
        if ($fieldValue && Storage::disk('public')->exists($fieldValue)) {
            Storage::disk('public')->delete($fieldValue);
        }

        $signature = str_replace('data:image/png;base64,', '', $stringSign);
        $signature = str_replace(' ', '+', $signature);
        $decodedImage = base64_decode($signature);

        $fileName = 'signatures/' . Str::uuid() . '.png';
        Storage::disk('public')->put($fileName, $decodedImage);
        return $fileName;
    }
}
