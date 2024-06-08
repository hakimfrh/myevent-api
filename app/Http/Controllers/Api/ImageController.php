<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    public function resizeImage($sourceImagePath, $maxWidth, $maxHeight)
    {
        list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourceImagePath);

        // Calculate new dimensions while maintaining aspect ratio
        $aspectRatio = $sourceWidth / $sourceHeight;
        if ($sourceWidth > $sourceHeight) {
            $targetWidth = $maxWidth;
            $targetHeight = $maxWidth / $aspectRatio;
        } else {
            $targetHeight = $maxHeight;
            $targetWidth = $maxHeight * $aspectRatio;
        }

        // Create a new image resource based on the original image type
        switch ($sourceType) {
            case IMAGETYPE_JPEG:
                $sourceImageResource = imagecreatefromjpeg($sourceImagePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImageResource = imagecreatefrompng($sourceImagePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImageResource = imagecreatefromgif($sourceImagePath);
                break;
            default:
                return false; // Unsupported image type
        }

        // Create a new true color image with the new dimensions
        $targetImageResource = imagecreatetruecolor($targetWidth, $targetHeight);

        // Resize the image to the new dimensions
        imagecopyresampled($targetImageResource, $sourceImageResource, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        // Output the resized image to a variable
        ob_start();
        switch ($sourceType) {
            case IMAGETYPE_JPEG:
                imagejpeg($targetImageResource);
                break;
            case IMAGETYPE_PNG:
                imagepng($targetImageResource);
                break;
            case IMAGETYPE_GIF:
                imagegif($targetImageResource);
                break;
        }
        $resizedImageData = ob_get_contents();
        ob_end_clean();

        // Free up memory
        imagedestroy($sourceImageResource);
        imagedestroy($targetImageResource);

        return $resizedImageData;
    }

    public function getImageBase64(Request $request)
    {
        // Define the path to the image
        // $path = 'img/' . $filename;
        $path = $request->image_path;

        // Check if the file exists
        if (!file_exists($path)) {
            return response()->json(['error' => 'Image not found. ' . $path], 404);
        }

        if (isset($request->w) && isset($request->h)) {
            $width = $request->w;
            $height = $request->h;
            $resizedImageData = $this->resizeImage($path, $width, $height);
            if ($resizedImageData === false) {
                return response()->json(['error' => 'Failed to resize image.'], 500);
            }

            // Encode the resized image to base64
            $base64 = base64_encode($resizedImageData);
            return response()->json(['base64Image' => $base64],200);
        }

        // Get the file content
        $fileContent = file_get_contents($path);
        // Encode the image to base64
        $base64 = base64_encode($fileContent);
        // Return the base64 string with the data URI scheme
        return response()->json(['base64Image' => $base64],200);
    }

    public function saveImage(Request $request)
    {
        $imageData = $request->image_data;
        $imageName = $request->image_name;
        $imagePath = $request->image_path;

        // // Decode the base64 image data
        $decoded_image = base64_decode($imageData);

        // // Save the received image data to a file
        $save_path = 'img/' . $imageName;

        $file_saved = file_put_contents($save_path, $decoded_image);

        if ($file_saved) {
            return response()->json(['message' => 'ok'],200);
        } else {
            return response()->json(['message' => 'failed saving file'],500);
        }
    }
}
