<?php

use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;

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

Route::get('/', function () {
    return view('admin-panel.welcome');
});

//Profile
Route::get('/profile', [ProfileController::class, 'index'])->middleware('auth')->name('profile.index');



