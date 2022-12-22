<?php

namespace App\Http\Controllers;

// require '/path/to/vendor/autoload.php';
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
            // 'profile' => 'default',
            'region' => 'ap-northeast-1',
            'version' => 'latest',
            // 'endpoint' => config('filesystems.disks.s3.client_url')
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

        return view('welcome',[
            'filename' => $filename,
            'pre_signed_url' => (string) $presignedRequest->getUri()
        ]);
    }

    public function oneTime($request){
        $s3 = S3Client::factory(['region' => getenv('AWS_REGION'), 'version' => '2006-03-01']);
        $command = $s3->getCommand('PutObject');
        $command['Bucket'] = 'example-test-s3';
        $command['Key'] = $_GET['name'];
        $result = $s3->createPresignedRequest($command, '+1 minutes');
        $data = ['uri' => (string)$result->getUri()];
        echo json_encode($data);
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
