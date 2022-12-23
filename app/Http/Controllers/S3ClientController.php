<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetPresignedUrlRequest;
use Aws\S3\S3Client;
use Illuminate\Support\Str;

class S3ClientController extends Controller
{
    /**
    * @param  \App\Http\Requests\GetPresignedUrlRequest  $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function getPresignedUrl(GetPresignedUrlRequest $request)
    {
        // S3クライアント作成
        $s3Client = new S3Client([
            'region' => 'ap-northeast-1',
            'version' => 'latest',
        ]);
        // GETリクエストのfailenameパラメーターを加工
        $filename = $this->makeUniqueFilename(
            pathinfo($request->filename, PATHINFO_EXTENSION)
        );
        // コマンド作成（何のアクション、どのバケットに、S3オブジェクト保存用のキー名を添えて）
        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $filename
        ]);
        // 署名済みURLの取得（コマンドと有効期限を添えて）
        $presignedRequest = $s3Client->createPresignedRequest(
            $cmd,
            '+5 minutes'
        );
        return response()->json(['filename' => $filename, 'pre_signed_url' => (string) $presignedRequest->getUri()]);
    }

    /**
    * @param  string $extension
    * @return string
    */

    // 一意のファイル名に加工するメソッド
    public function makeUniqueFilename($extenstion)
    {
        return (string) Str::uuid() . '.' . $extenstion;
    }
}
