<?php

use App\Http\Controllers\DrugsAndMedicalProductController;
use App\Http\Controllers\MedicationKnowledgeController;
use App\Http\Controllers\MedicationResponseController;
use App\Models\BnfDetail;
use App\Models\DmAndDBrowser;
use App\Models\DmAndDIngredient;
use App\Models\MedicationResponse;
use App\Models\NafdacProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/test', [DrugsAndMedicalProductController::class, 'start_creating_responses']);

