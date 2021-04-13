<?php

use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    // Users CRUD
    Route::resource('users', 'UserController');

    // Invoices CRUD
    Route::resource('invoices', 'InvoiceController');
    Route::post('/invoices/{invoice}/send', 'InvoiceController@send')->name('invoices.send');
    Route::get('/invoices/{invoice}/pdf', 'InvoiceController@downloadPdf')->name('invoices.pdf');
    Route::post('/invoices/{invoice}/email', 'InvoiceController@sendEmail')->name('invoices.email');

    // Payments
    Route::get('/invoices/{invoice}/payments/create', 'PaymentController@create')->name('payments.create');
    Route::post('/invoices/{invoice}/payments', 'PaymentController@store')->name('payments.store');
    Route::delete('/payments/{payment}', 'PaymentController@destroy')->name('payments.destroy');
});
