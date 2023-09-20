<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Models\User;
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

Route::get('/', [Controller::class, 'getAllFiles']);
Route::post("/upload", [Controller::class, 'upload']);
Route::get("/get-all", [Controller::class, 'getAllFiles']);
Route::get("/get-file/{file_id}", [Controller::class, 'getMongoDBFile']);
Route::get("/delete-file/{file_id}", [Controller::class, 'deleteFileFromMongoDB']);
