<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-log', function() {
    \Log::info(' TESTING LOG - ' . now());
    \Log::debug('Debug log test');
    \Log::error('Error log test');
    
    // Also try direct file write
    file_put_contents(
        storage_path('logs/manual-test.txt'),
        date('Y-m-d H:i:s') . " - Direct write test\n",
        FILE_APPEND
    );
    
    return 'Check logs now! Look for: storage/logs/laravel-' . date('Y-m-d') . '.log';
});
