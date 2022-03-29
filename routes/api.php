<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WorkerController;
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

Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
});

//Admin Routes
Route::post('/admin/signup',[AdminController::class,'store']);
Route::prefix('admin')->middleware(['auth:sanctum','role:Admin'])->group(function () {
    Route::get('/', [AdminController::class,'index']);
});
//Route::middleware('auth:sanctum')->get('/admin', [AdminController::class,'index'])->middleware('role:Admin');

//Customer Routes
Route::post('/customer/signup',[CustomerController::class,'store']);
Route::prefix('customer')->middleware(['auth:sanctum','role:Customer'])->group(function () {
    Route::get('/', [CustomerController::class,'index']);
});

//Worker Routes
Route::post('/worker/signup',[WorkerController::class,'store']);
Route::prefix('worker')->middleware(['auth:sanctum','role:Worker'])->group(function () {
    Route::get('/', [WorkerController::class,'index']);
});
