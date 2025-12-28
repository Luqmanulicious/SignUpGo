<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegistrationApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API endpoints for registration data
// Your friend can use these endpoints to pull registration data

// Get all registrations with optional filters
// Example: GET /api/registrations?event_id=1&role=participant&status=approved
Route::get('/registrations', [RegistrationApiController::class, 'getRegistrations']);

// Get registrations for a specific event
// Example: GET /api/events/1/registrations
Route::get('/events/{eventId}/registrations', [RegistrationApiController::class, 'getEventRegistrations']);

// Get registration statistics
// Example: GET /api/registrations/stats or GET /api/events/1/registrations/stats
Route::get('/registrations/stats', [RegistrationApiController::class, 'getRegistrationStats']);
Route::get('/events/{eventId}/registrations/stats', [RegistrationApiController::class, 'getRegistrationStats']);
