<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use Exception;

class ImgbbService
{
    /**
     * Upload an image to ImgBB and return the display URL.
     *
     * @param UploadedFile $file
     * @return string
     * @throws Exception
     */
    public function uploadImage(UploadedFile $file): string
    {
        $apiKey = env('IMGBB_API_KEY');
        
        if (!$apiKey) {
            throw new Exception("IMGBB_API_KEY is not set in the environment.");
        }

        // Convert the image to base64
        $imageData = base64_encode(file_get_contents($file->path()));

        $response = Http::asForm()->post('https://api.imgbb.com/1/upload', [
            'key' => $apiKey,
            'image' => $imageData,
        ]);

        if ($response->successful()) {
            return $response->json('data.url');
        }

        throw new Exception("Failed to upload image to ImgBB: " . $response->body());
    }
}
