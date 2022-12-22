<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\S3ClientController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/s3/presignedurl', [S3ClientController::class, 'getPresignedUrl'])->name('s3.getPresignedUrl');