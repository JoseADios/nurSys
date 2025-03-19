<?php

// app/Services/FirmService.php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FirmService
{
    public function createImag($stringSign, $fieldValue)
    {
        // Eliminar imagen anterior si existe
        if ($fieldValue && Storage::disk('public')->exists($fieldValue)) {
            Storage::disk('public')->delete($fieldValue);
        }

        // Limpiar y decodificar la cadena base64
        $signature = str_replace(['data:image/png;base64,', ' '], ['', '+'], $stringSign);
        $decodedImage = base64_decode($signature);

        // Normalizar tamaño de imagen
        $tempImage = imagecreatefromstring($decodedImage);
        if (!$tempImage) {
            return null; // Manejar error de imagen inválida
        }

        try {
            $width = imagesx($tempImage);
            $height = imagesy($tempImage);

            // Dimensiones normalizadas
            $normalizedWidth = 600;
            $normalizedHeight = (int) (($height / $width) * $normalizedWidth);

            // Crear imagen con fondo transparente
            $normalizedImage = imagecreatetruecolor($normalizedWidth, $normalizedHeight);
            imagealphablending($normalizedImage, false);
            imagesavealpha($normalizedImage, true);
            $transparent = imagecolorallocatealpha($normalizedImage, 255, 255, 255, 127);
            imagefilledrectangle($normalizedImage, 0, 0, $normalizedWidth, $normalizedHeight, $transparent);

            // Redimensionar
            imagecopyresampled(
                $normalizedImage,
                $tempImage,
                0,
                0,
                0,
                0,
                $normalizedWidth,
                $normalizedHeight,
                $width,
                $height
            );

            // Obtener datos de la imagen usando buffer de memoria
            ob_start();
            imagepng($normalizedImage, null, 9); // Máxima compresión
            $normalizedData = ob_get_clean();

            // Generar nombre de archivo y guardar
            $fileName = 'signatures/' . Str::uuid() . '.png';
            Storage::disk('public')->put($fileName, $normalizedData);

            return $fileName;
        } finally {
            // Liberar memoria siempre, incluso si hay excepciones
            if (isset($tempImage))
                imagedestroy($tempImage);
            if (isset($normalizedImage))
                imagedestroy($normalizedImage);
        }
    }
}
