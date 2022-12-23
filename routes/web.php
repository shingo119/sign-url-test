<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\S3ClientController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/s3/presignedurl', [S3ClientController::class, 'getPresignedUrl'])->name('s3.getPresignedUrl');