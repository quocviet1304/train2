<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;

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


Route::get('/category_model', [WebController::class, 'index'])->name('web.show');
Route::get('/area', [WebController::class, 'handleUrl'])->name('web.area');
Route::post('/ajax/getProducts', [WebController::class, 'loadData'])->name('web.loadData');
