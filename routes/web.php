<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MembersController;

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

Route::get('/form', function () {
    return view('forms');
});

Route::post('/form', [MembersController::class, 'store']);


Route::get('/members', [MembersController::class, 'index']);
