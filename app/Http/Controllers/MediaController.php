<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{

public function uploadfile(Request $request)
    {
        // Validate the incoming request, ensure it has a file.
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,gif|max:2048', // You can adjust the allowed file types and size as needed.
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Upload the file to your S3 bucket.
        $path = Storage::disk('s3')->put('horlakz', $file);

        // Generate the URL with the S3 bucket name and the path.
        $s3Url = Storage::disk('s3')->url($path);

        return response()->json(['url' => $s3Url, 'filename' =>  $fileName], 201);
    }

    public function allMedia()
    {
        // Getting a list of files in the "horlakz" directory in the S3 bucket.
        $files = Storage::disk('s3')->files('horlakz');

        $fileUrls = array_map(function ($file) {
            return Storage::disk('s3')->url($file);
        }, $files);

        return response()->json(['files' => $files, 'fileUrls' => $fileUrls], 200);
    }
    
    public function getMedia($filename)
    {
        // Check if the specified file exists in the "horlakz" directory in your S3 bucket.
        if (Storage::disk('s3')->exists('horlakz/' . $filename)) {
            // Generate the URL for the specified file and provide it for download.
            $fileUrl = Storage::disk('s3')->url('horlakz/' . $filename);
    
            return response()->json(['file_url' => $fileUrl], 200);
        } else {
            return response()->json(['message' => 'File not found'], 404);
        }
    }
    
    /**
     * Delete a media by its filename.
     */
    public function deleteMedia($filename)
    {
        // Checking if the specified file exists in the "horlakz" directory in S3 bucket.
        if (Storage::disk('s3')->exists('horlakz/' . $filename)) {
            // Delete the specified file from the S3 bucket.
            Storage::disk('s3')->delete('horlakz/' . $filename);
    
            return response()->json(['message' => 'File deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'File not found'], 404);
        }
    }
    

      /**
     * Delete selected media by providing an array of filenames.
     */
    public function deleteSelectedMedia(Request $request)
    {
        // Get the list of file names to delete from the request's JSON data.
        $filenames = $request->json('filenames');
        
        $deletedFiles = [];
        
        // Loop through the list of filenames and delete each file.
        foreach ($filenames as $filename) {
            if (Storage::disk('s3')->exists('horlakz/' . $filename)) {
                Storage::disk('s3')->delete('horlakz/' . $filename);
                $deletedFiles[] = $filename;
            }
        }
        
        if (empty($deletedFiles)) {
            return response()->json(['message' => 'No files found for deletion'], 404);
        }
        
        return response()->json(['message' => 'Files deleted successfully', 'deleted_files' => $deletedFiles], 200);
    }
    
}
