<?php

namespace App\Http\Controllers;

// use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use Illuminate\Foundation\Bus\DispatchesJobs;
// use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    // use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function upload(Request $request)
    {
        $validator = $request->validate([
            'file' => 'required',
        ]);
        //upload file
        $bucket = DB::connection('mongodb')->getMongoDB()->selectGridFSBucket(['bucketName' => 'files']);
        $resource = fopen($request->file('file'), "a+");
        $extension = $request->file('file')->extension() ?: 'txt';
        // return $extension;
        $file_id = $bucket->uploadFromStream(
            Str::random(25) . strtotime(now()) . '.' . $extension,
            $resource,
            [
                "metadata" => [
                    'mimeType' => File::mimeType($request->file('file')),
                    'file_name' => $request->file('file')->getClientOriginalName()
                ]
            ]
        );
        $response = response()->json(["error" => "Failed to upload the file"], 500);
        if ($file_id) {
            $response = redirect("/");
        }
        return $response;
    }
    public function getAllFiles()
    {
        $bucket = DB::connection('mongodb')->getMongoDB()->selectGridFSBucket(['bucketName' => 'files']);
        $files = $bucket->find();
        return view('upload', ["files" => $files]);
    }
    public function deleteFileFromMongoDB(Request $request, $file_id)
    {
        $bucket = DB::connection('mongodb')->getMongoDB()->selectGridFSBucket(['bucketName' => 'files']);
        $file_id = new \MongoDB\BSON\ObjectID($file_id);
        $file = $bucket->findOne(["_id" => $file_id]);
        $response = response()->json(["error" => "Invalid file id given"], 500);
        if ($file) {
            $bucket->delete($file_id);
            $response = redirect("/");
        }
        return $response;
    }
    public function getMongoDBFile(Request $request, $file_id)
    {
        $bucket = DB::connection('mongodb')->getMongoDB()->selectGridFSBucket(['bucketName' => 'files']);
        $file_id = new \MongoDB\BSON\ObjectID($file_id);
        $file = $bucket->findOne(["_id" => $file_id]);

        $response = response()->json(["error" => "Invalid file id given"], 500);

        if ($file && in_array($file->metadata->mimeType, ['image/png', 'image/jpg', 'image/jpeg', 'image/gif', 'video/mp4'])) {
            $downloadStream = $bucket->openDownloadStream($file_id);
            $stream = stream_get_contents($downloadStream, -1);
            $response = response($stream, 200, ['Content-Type' => $file->metadata->mimeType]);
        } else if ($file) {
            $file_name_with_extension = $file->filename;
            $file_name_with_extension = storage_path("app/avatars") . "/" . $file_name_with_extension;
            if (!file_exists($file_name_with_extension)) {
                $downloadStream = $bucket->openDownloadStream($file_id);
                $stream = stream_get_contents($downloadStream, -1);
                $ifp = fopen($file_name_with_extension, "a+");
                fwrite($ifp, $stream);
                fclose($ifp);
            }
            $response = response()->download($file_name_with_extension, $file->metadata->file_name, [
                'Content-Type' =>  $file->metadata->mimeType,
            ])->deleteFileAfterSend(true);
        }
        return $response;
        // return $file_metadata;
        // $path = $file->filename;

        // // if(!file_exists($path)) {
        // $downloadStream = $bucket->openDownloadStream($file_id);
        // $stream = stream_get_contents($downloadStream, -1);
        // return response($stream, 200, ['Content-Type' => $file_metadata->metadata->mimeType]);
        // $ifp = fopen($path, "a+");
        // fwrite($ifp, $stream);
        // fclose($ifp);
        // // }
    }

    // public function getAllFiles(Request $request)
    // {
    //     // if ($request->hasFile('file')) {
    //     //     $path = Storage::putFile('avatars', $request->file('file'));
    //     //     return $path;
    //     // }

    //     // $files = Storage::disk("private")->files();
    //     // return response(File::get(storage_path("app/avatars/" . $files[0])), 200, ['Content-Type' => File::mimeType(storage_path("app/avatars/" . $files[0]))]);
    // }


    // example code
    //upload file
    // $bucket = \DB::connection('mongodb')->getMongoDB()->selectGridFSBucket();
    // $resource = fopen($file_path, "a+");
    // $file_id = $bucket->uploadFromStream($file_path, $resource);

    // //download file
    // $bucket = \DB::connection('mongodb')->getMongoDB()->selectGridFSBucket();
    // $file_metadata = $bucket->findOne(["_id" => $file_id]);
    // $path = $file_metadata->filename;

    // if(!file_exists($path)) {
    //     $downloadStream = $bucket->openDownloadStream($file_id);
    //     $stream = stream_get_contents($downloadStream, -1);
    //     $ifp = fopen($path, "a+");
    //     fwrite($ifp, $stream);
    //     fclose($ifp);
    // }
}
