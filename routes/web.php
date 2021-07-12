<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

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

Route::post('/api/register', [RegisterController::class, 'register'])->name('register');
Route::post('/api/login', [LoginController::class, 'login'])->name('login');
Route::post('/api/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/api/user', fn() => Auth::user())->name('user');

Route::get('/{any?}', fn() => view('index'))->where('any', '.+');