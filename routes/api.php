<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//user register
Route::post('/user/register', [AuthController::class, 'userRegister']);

//restauran register
Route::post('/restaurant/register', [AuthController::class, 'restaurantRegister']);
//driver register
Route::post('/driver/register', [AuthController::class, 'driverRegister']);

//login
Route::post('/login', [AuthController::class, 'login']);
//logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

//update laltong user
Route::put('/user/update-latlong', [AuthController::class,'updateLatLong'])->middleware('auth:sanctum');

//get all restaurant
Route::get('/restaurant', [AuthController::class,'getAllRestaurant']);

//get all product
Route::apiResource('/products', ProductController::class)->middleware('auth:sanctum');

//order
Route::post('/order', [OrderController::class, 'createOrder'])->middleware('auth:sanctum');

//update purchase status
Route::put('/order/user/update-status/{id}', [OrderController::class, 'updatePurchaseStatus'])->middleware('auth:sanctum');

//get order by user id
Route::get('/order/user', [OrderController::class, 'orderHistory'])->middleware('auth:sanctum');


//get order by restaurant id
Route::get('/order/restaurant', [OrderController::class, 'getOrdersByStatusRestaurant'])->middleware('auth:sanctum');

//update order status for restaurant
Route::put('/order/restaurant/update-status/{id}', [OrderController::class, 'updateOrderStatusRestaurant'])->middleware('auth:sanctum');


// get order by driver
Route::get('/order/driver', [OrderController::class, 'getOrderByStatusForDriver'])->middleware('auth:sanctum');

//update order status for driver
Route::put('/order/driver/update-status/{id}', [OrderController::class, 'updateOrderStatusForDriver'])->middleware('auth:sanctum');

//update purchase status
Route::put('/order/user/update-status/{id}', [OrderController::class, 'updatePurchaseStatus'])->middleware('auth:sanctum');
