<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemuserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Api\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
// Route::get('/', function () {
//     return "Success!";
// });

// testing cors
Route::get('/test-cors', function (Request $request) {
    return response()->json(['message' => 'CORS is working']);
});

// connecting Angular and Laravel using Cors
Route::group(['middleware' => Cors::class], function () {
    Route::apiResource('systemusers', SystemuserController::class);
});

// -------------------------------------------
// get customer data
Route::get('/getUser', [UserController::class, 'getUser']);

Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});
         
Route::middleware('auth:sanctum')->group( function () {
    Route::resource('products', ProductController::class);
});

// for login and registration
Route::post('/auth/register', [UserController::class, 'createUser']);
Route::post('/auth/login', [UserController::class, 'loginUser']);
Route::post('/auth/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/users/address/{id}', [UserController::class, 'getAddress']);

// Route::apiResource('systemusers', SystemuserController::class);

// menus,cart, and order management
Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);
// Route::apiResource('carts', CartController::class);
// store to the cart
Route::post('/carts', [CartController::class, 'store'])->middleware('auth:sanctum');
Route::post('/carts/update/{cart}', [CartController::class, 'update'])->middleware('auth:sanctum');
Route::delete('carts/{cart}', [CartController::class, 'destroy']);



// get carts using userID
Route::get('carts/{user_id}', [CartController::class, 'showByUser']);

