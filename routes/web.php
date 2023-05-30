<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get("/",[UserController::class,"getUser"])->name("user_data");
Route::post("store-user",[UserController::class,"storeUser"])->name("store_user");
Route::post("update-user",[UserController::class,"updateUser"])->name("update_user");
Route::get("edit-user",[UserController::class,"editUser"])->name("edit_user");
Route::get("delete-user",[UserController::class,"deleteUser"])->name("delete_user");
