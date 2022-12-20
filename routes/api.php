<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
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

Route::post('create-customer', [CustomerController::class,'create'])->name('createCustomer');
Route::get('list-customer', [CustomerController::class,'list'])->name('listCustomer');
Route::get('retrive-customer', [CustomerController::class,'retrive'])->name('retriveCustomer');
Route::post('update-customer', [CustomerController::class,'update'])->name('updateCustomer');
Route::delete('delete-customer', [CustomerController::class,'delete'])->name('deleteCustomer');