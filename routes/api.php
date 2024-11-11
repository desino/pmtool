<?php

use App\Http\Controllers\Api\AllTicketsWithoutInitiativeController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\SocialiteController;
use App\Http\Controllers\Api\BulkCreateTicketsController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DeploymentCenterController;
use App\Http\Controllers\Api\DeploymentController;
use App\Http\Controllers\Api\HeaderController;
use App\Http\Controllers\Api\HomeMyActionsController;
use App\Http\Controllers\Api\InitiativeController;
use App\Http\Controllers\Api\InitiativeTimeBookingController;
use App\Http\Controllers\Api\MyTicketController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\PlanningController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\RestApiController;
use App\Http\Controllers\Api\SolutionDesignController;
use App\Http\Controllers\Api\TestCaseController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TimeBookingController;
use App\Http\Controllers\Api\UserController;
use App\Models\Planning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Ramsey\Uuid\Type\Time;

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/user', 'user');
    });

    Route::controller(DeploymentCenterController::class)->prefix('deployment-center')->group(function () {
        Route::get('/', 'index');
        Route::get('/get-test-deployment-tickets-modal-data/{initiative_id}', 'getTestDeploymentTicketsModalData');
        Route::post('submit-test-deployment-ticket/{initiative_id}', 'submitTestDeploymentTicket');
        Route::get('get-acceptance-deployment-tickets-modal-data/{initiative_id}', 'getAcceptanceDeploymentTicketsModalData');
        Route::post('submit-acceptance-deployment-ticket/{initiative_id}', 'submitAcceptanceDeploymentTicket');
        Route::get('get-production-deployment-tickets-modal-data/{initiative_id}', 'getProductionDeploymentTicketsModalData');
        Route::post('submit-production-deployment-ticket/{initiative_id}', 'submitProductionDeploymentTicket');
    });

    Route::controller(HomeMyActionsController::class)->prefix('home-my-actions')->group(function () {
        Route::get('/', 'index');
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
        Route::get('get-edit-opportunity-data', 'getEditOpportunityData');
        Route::get('get-opportunity/{id}', 'getOpportunity');
    });

    Route::controller(SolutionDesignController::class)->prefix('solution-design')->group(function () {
        Route::post('/', 'index');
        Route::post('/download-list', 'downloadList');
        Route::post('/download-pdf', 'downloadPDF');
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
            Route::post('update-ticket/{ticket_id}', 'updateTicket');
            Route::post('change-action-user/{ticket_id}', 'changeActionUser');
            Route::post('change-action-status/{ticket_id}', 'changeActionStatus');
            Route::post('change-previous-action-status/{ticket_id}', 'changePreviousActionStatus');
            Route::get('get-create-release-data', 'getCreateReleaseData');
            Route::post('create-release', 'createRelease');
            Route::post('update-ticket-detail-estimated-hours/{ticket_id}', 'updateTicketDetailEstimatedHours');
            Route::post('add-remove-priority', 'addRemovePriority');
            Route::post('mark-as-visible-invisible', 'markAsVisibleInvisible');

            Route::controller(TestCaseController::class)->prefix('{ticket_id}/test-case')->group(function () {
                Route::post('/store', 'store');
                Route::post('/update/{test_case_id}', 'update');
                Route::get('/show/{test_case_id}', 'show');
            });
        });

        Route::controller(ProjectController::class)->prefix('{initiative_id}/project')->group(function () {
            Route::post('/', 'index');
            Route::post('/change-status', 'changeStatus');
            Route::post('/update', 'update');
        });

        Route::controller(MyTicketController::class)->prefix('{initiative_id}/my-ticket')->group(function () {
            Route::get('/', 'index');
        });

        Route::controller(BulkCreateTicketsController::class)->prefix('{initiative_id}/bulk-create-tickets')->group(function () {
            Route::get('/', 'index');
            Route::post('/store-new-bulk-tickets', 'storeNewBulkTickets');
        });

        Route::controller(DeploymentController::class)->prefix('{initiative_id}/deployments')->group(function () {
            Route::get('/', 'index');
            Route::get('/get-initiative-data-for-deployments', 'getInitiativeDataForDeployments');
            Route::post('/download-release-notes', 'downloadReleaseNotes');
            Route::post('/download-test-results', 'downloadTestResults');
        });
    });

    Route::controller(HeaderController::class)->prefix('header')->group(function () {
        Route::get('/get-initiatives', 'getInitiatives');
    });

    Route::controller(TimeBookingController::class)->prefix('time-booking')->group(function () {
        Route::get('/', 'index');
        Route::get('/get-time-booking-initial-data', 'getTimeBookingInitialData');
        Route::get('/get-time-booking-modal-initial-data', 'getTimeBookingModalInitialData');
        Route::post('/store', 'store');
        Route::get('/get-time-booking-on-new-ticket-modal-initial-data', 'getTimeBookingOnNewTicketModalInitialData');
        Route::get('get-time-booking-on-new-initiative-or-ticket-modal-initial-data', 'getTimeBookingOnNewInitiativeOrTicketModalInitialData');
        Route::post('/store-time-booking-on-new-ticket', 'storeTimeBookingOnNewTicket');
        Route::post('/store-time-booking-on-new-initiative-or-ticket', 'storeTimeBookingOnNewInitiativeOrTicket');
        Route::post('/delete-time-bookings', 'deleteTimeBookings');
        Route::get('/fetch-tickets', 'fetchTickets');
        Route::post('/store-time-booking-for-ticket-detail', 'storeTimeBookingForTicketDetail');
        Route::post('/store-time-booking-for-un-billable', 'storeTimeBookingForUnBillable');
        Route::get('/get-time-booking-un-billable-modal-initial-data', 'getTimeBookingUnBillableModalInitialData');
    });

    Route::controller(PlanningController::class)->prefix('planning')->group(function () {
        Route::get('/', 'index');
        Route::get('/get-planning-initial-data', 'getPlanningInitialData');
        Route::post('/store-planning', 'storePlanning');
    });

    Route::controller(InitiativeTimeBookingController::class)->prefix('initiative-time-booking')->group(function () {
        Route::get('/', 'index');
        Route::get('/get-initial-data-for-initiative-time-bookings', 'getInitialDataForInitiativeTimeBookings');
        Route::get('/get-project-list-for-initiative-time-bookings', 'getProjectListForInitiativeTimeBookings');
        Route::post('/assign-project-for-initiative-time-bookings', 'assignProjectForInitiativeTimeBookings');
    });

    Route::controller(AllTicketsWithoutInitiativeController::class)->prefix('all-tickets-without-initiative')->group(function () {
        Route::get('/', 'index');
        Route::get('get-initial-data', 'getInitialData');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
    Route::post('/forgot-password', 'sendResetLink');
    Route::post('/reset-password', 'resetPassword');
});

Route::middleware(['checkApiKey'])->group(function () {
    Route::controller(RestApiController::class)->group(function () {
        Route::get('/database-table-data', 'databaseTableData');
    });
});
