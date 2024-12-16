<?php

use App\Actions\Fortify\UpdateUserPassword;
use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\CandidateRatingController;
use App\Http\Controllers\Api\CandidateReviewController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\VacancyController;
use App\Http\Controllers\Api\VacancyRatingController;
use App\Http\Controllers\Api\VacancyReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('role');
});

//profile
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::put('auth/edit/{id}', [AuthController::class, 'edit'])->middleware('auth:sanctum');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::post('/upload-avatar', [AuthController::class, 'uploadAvatar']);

//cities
Route::get('cities', [CityController::class, 'index']);

//categories
Route::get('categories', [CategoryController::class, 'index']);

//company
Route::post('create-company', [CompanyController::class, 'create']);
Route::get('companies', [CompanyController::class, 'getUserCompanies']);
Route::delete('/companies-delete/{id}', [CompanyController::class, 'deleteCompany']);

//vacancies
Route::post('create-vacancy', [VacancyController::class, 'create']);
Route::get('vacancies', [VacancyController::class, 'getVacancies']);
Route::put('vacancies-toggle-status/{id}', [VacancyController::class, 'toggleStatus']);
Route::delete('vacancies-delete/{id}', [VacancyController::class, 'deleteVacancy']);
Route::get('vacancies/{id}', [VacancyController::class, 'getVacancyById']);
Route::post('vacancies-update/{id}', [VacancyController::class, 'updateVacancy']);
Route::get('all-vacancies', [VacancyController::class, 'getAllVacancies']);
Route::get('single-vacancy/{id}', [VacancyController::class, 'getSingVacancy']);

//candidate
Route::post('create-candidate-info', [CandidateController::class, 'createCandidateInfo']);
Route::get('candidates', [CandidateController::class, 'getCandidate']);
Route::post('update-candidate-info', [CandidateController::class, 'updateCandidateInfo']);
Route::get('candidate-toggle-status', [CandidateController::class, 'toggleCandidateStatus']);
Route::get('clear-candidate-info', [CandidateController::class, 'clearCandidateInfo']);
Route::get('all-candidates', [CandidateController::class, 'getAllCandidates']);
Route::get('single-candidate/{id}', [CandidateController::class, 'getSingCandidate']);

//review
Route::post('create-review-vacancy', [VacancyReviewController::class, 'createVacancyReview']);
Route::post('create-review-candidate', [CandidateReviewController::class, 'createCandidateReview']);

//rating
Route::post('create-rating-candidate', [CandidateRatingController::class, 'createRatingForCandidate']);
Route::post('create-rating-vacancy', [VacancyRatingController::class, 'createRatingForVacancy']);

//message
Route::post('/messages', [MessageController::class, 'store']);
Route::get('/messages/{user}', [MessageController::class, 'index']);





