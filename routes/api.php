<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\API\{ServiceController , ContactController , SponserController , MemberController, ProjectController , ImageProjectController,StatisticsController };

Route::apiResource('services' , ServiceController::class) ;
Route::apiResource('contact' , ContactController::class) ;
Route::apiResource('sponsers' , SponserController::class) ;
Route::apiResource('members' , MemberController::class) ;
Route::apiResource('projects' , ProjectController::class) ;
Route::apiResource('image-project' , ImageProjectController::class) ;
Route::get('statistics' , [StatisticsController::class,'getStatistics']) ;

Route::match(['post', 'put', 'patch'], 'sponsers/{id}', [SponserController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'projects/{id}', [ProjectController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'image-project/{id}', [ImageProjectController::class, 'update']);
Route::match(['post', 'put', 'patch'], 'members/{id}', [MemberController::class, 'update']);

Route::middleware(['api'])->prefix('admin')->group(function() {
    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/logout', [AdminController::class, 'logout']);
    Route::post('/register', [AdminController::class, 'register']);
    Route::post('/resetPassword', [AdminController::class, 'resetPassword']);
    Route::get('/getaccount', [AdminController::class, 'getaccount']);
});
