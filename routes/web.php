<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

// Log para debug
Log::info('Web Routes loaded');

// Rota para o frontend Vue
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api).*$');
