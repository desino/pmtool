<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\SocialiteController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\HeaderController;
use App\Http\Controllers\Api\InitiativeController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SolutionDesignController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/user', 'user');
    });

    Route::controller(ClientController::class)->prefix('client')->group(function () {
        Route::post('/store', 'store');
    });

    Route::controller(InitiativeController::class)->prefix('initiative')->group(function () {
        Route::get('/get-clients', 'getClients');
        Route::post('/store', 'store');
    });

    Route::controller(OpportunityController::class)->prefix('opportunity')->group(function () {
        Route::post('/', 'index');
        Route::get('/get-initial-data', 'getInitialData');
        Route::post('/update', 'update');
        Route::post('/update-status-lost', 'updateStatusLost');
        Route::get('get-client-list', 'getClientList');
        Route::get('get-user-list', 'getUserList');
        Route::get('get-opportunity/{id}', 'getOpportunity');
    });

    Route::controller(SolutionDesignController::class)->prefix('solution-design')->group(function () {
        Route::post('/', 'index');
        Route::post('/get-initiative', 'getInitiative');
        Route::post('/store-section', 'storeSection');
        Route::post('/store-update-functionality', 'storeUpdateFunctionality');
        Route::post('/delete-functionality', 'deleteFunctionality');
        Route::post('/delete-section', 'deleteSection');
        Route::post('/update-section', 'updateSection');
        Route::post('/update-functionality-order-no', 'updateFunctionalityOrderNo');
        Route::post('/update-section-order-no', 'updateSectionOrderNo');

        Route::controller(TicketController::class)->prefix('{initiative_id}/ticket')->group(function () {
            Route::get('all', 'index');
            Route::post('store', 'store');
            Route::get('show/{ticket_id}', 'show');
            Route::get('all-ticket/', 'allTicketsForDropdown');
            Route::post('update-release-note/{ticket_id}', 'updateReleaseNote');
            Route::get('get-initiative-project-list', 'getInitiativeProjectList');
            Route::post('assign-project', 'assignProject');
            Route::post('assign-or-remove-project-for-task', 'assignOrRemoveProjectForTask');
            Route::get('get-initial-data-for-create-or-edit-ticket', 'getInitialDataForCreateOrEditTicket');
            Route::get('edit-ticket/{ticket_id}', 'editTicket');
        });

        Route::controller(ProjectController::class)->prefix('{initiative_id}/project')->group(function () {
            Route::post('/', 'index');
            Route::post('/change-status', 'changeStatus');
            Route::post('/update', 'update');
        });
    });



    Route::controller(HeaderController::class)->prefix('header')->group(function () {
        Route::get('/get-initiatives', 'getInitiatives');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
    Route::post('/forgot-password', 'sendResetLink');
    Route::post('/reset-password', 'resetPassword');
});
