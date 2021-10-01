<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\DashboardController;

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

Route::get('/', [PostController::class, 'index'])->name('posts.index');


Route::middleware(['auth'])->group(function(){

    Route::resource('posts', PostController::class)->except('index'); //Pour lui dire de ne pas gérer la route home / index

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});





require __DIR__.'/auth.php';
