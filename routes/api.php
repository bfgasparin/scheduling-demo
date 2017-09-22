<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| is assigned the "api" middleware group. Enjoy building your API!
| routes are loaded by the RouteServiceProvider within a group which
|
*/

Route::post('users', 'UserController@store');

Route::post('userActivation/token', 'UserActivationController@sendNewToken');
Route::post('userActivation', 'UserActivationController@store');

Route::post('users/login', 'Auth\UserLoginController@login');

Route::namespace('User')->group(function () {
    Route::resource('bookings', 'BookingController', ['only' => ['index']]);

    Route::prefix('salons/{salon}')->group(function () {
        Route::resource('bookings', 'SalonBookingController', ['except' => ['create', 'edit', 'update']]);
    });
});

Route::apiResource('salons', 'SalonController');
Route::get('salonCategories', 'SalonCategoryController@index');

Route::post('professional/login', 'Auth\Salon\ProfessionalLoginController@login');
Route::post('admin/login', 'Auth\Salon\AdminLoginController@login');

Route::prefix('salons/{salon}')->group(function () {
    Route::resource('professionals', 'SalonProfessionalController', ['only' => ['index', 'show']]);
    Route::get('professionals/{professional}/calendar', 'SalonProfessionalCalendarController@index');

    Route::resource('services', 'SalonServiceController', ['only' => ['index', 'show']]);
});

Route::namespace('Salon')->group(function () {
    Route::apiResource('salonServices', 'ServiceController');

    Route::apiResource('employees', 'EmployeeController');
    Route::resource('professionals', 'ProfessionalController', ['only' => ['index', 'show']]);
    Route::get('professionals/{professional}/calendar', 'ProfessionalCalendarController@index');
    Route::resource('professionals/{professional}/services', 'ProfessionalServiceController', ['only' => ['index', 'store']]);
    Route::resource('professionals/{professional}/workingJorneys', 'ProfessionalWorkingJorneyController', ['only' => ['show', 'update', 'store']]);

    Route::apiResource('professionalWorkingJorneys/{workingJorney}/schedules', 'Professional\WorkingJorneyScheduleController');
    Route::apiResource('professionalWorkingJorneys/{workingJorney}/absences', 'Professional\WorkingJorneyAbsenceController');

    Route::resource('clientBookings', 'BookingController', ['only' => ['index']]);
    Route::resource('clients/{client}/bookings', 'ClientBookingController', ['except' => ['create', 'edit', 'update']]);

    Route::namespace('Professional')->group(function () {
        Route::get('calendar', 'CalendarController@index');

        Route::resource('services', 'ServiceController', ['only' => ['index', 'store']]);

        Route::resource('workingJorney', 'WorkingJorneyController', ['only' => ['show', 'update', 'store']]);
        Route::apiResource('workingJorneys/{workingJorney}/schedules', 'WorkingJorneyScheduleController');
        Route::apiResource('workingJorneys/{workingJorney}/absences', 'WorkingJorneyAbsenceController');
    });
});

Route::post('nexmo/callback', 'NexmoCallbackController@store');

