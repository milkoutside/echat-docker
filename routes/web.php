<?php

use App\Events\TestEvent;
use Illuminate\Support\Facades\Route;

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

// SPA fallback, исключая API и статику
Route::get('/{page}', function () {
    return view('app');
})->where('page', '^(?!api|build|libs|storage|robots\.txt|favicon\.ico).*$');
