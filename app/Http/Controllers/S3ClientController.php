<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetPresignedUrlRequest;
use Aws\S3\S3Client;
use Aws\S3\Transfer;
use Aws\Exception\AwsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class S3ClientController extends Controller
{
    /**
    * @param  \App\Http\Requests\GetPresignedUrlRequest  $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function getPresignedUrl(GetPresignedUrlRequest $request)
    {
        $s3Client = new S3Client([
            'region' => 'ap-northeast-1',
            'version' => 'latest',
        ]);

        $filename = $this->makeUniqueFilename(
            pathinfo($request->filename, PATHINFO_EXTENSION)
        );

        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $filename
        ]);

        $presignedRequest = $s3Client->createPresignedRequest(
            $cmd,
            '+5 minutes'
        );

        // return view('welcome',[
        //     'filename' => $filename,
        //     'pre_signed_url' => (string) $presignedRequest->getUri()
        // ]);
        return response()->json(['filename' => $filename, 'pre_signed_url' => (string) $presignedRequest->getUri()]);
    }

    /**
    * @param  string $extension
    * @return string
    */
    public function makeUniqueFilename($extenstion)
    {
        return (string) Str::uuid() . '.' . $extenstion;
    }
}
