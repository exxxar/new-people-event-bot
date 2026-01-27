<?php


use App\Http\Controllers\EventRequestsController;

use App\Http\Controllers\HelpFormatController;
use App\Http\Controllers\IncomingReportsController;
use App\Http\Controllers\IssueCategoryController;
use App\Http\Controllers\MunicipalityController;

use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ResultReportsController;

use App\Http\Controllers\UserController;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

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


Route::any('/register-webhook', [\App\Http\Controllers\TelegramController::class, "registerWebhooks"]);
Route::post('/webhook', [\App\Http\Controllers\TelegramController::class, "handler"]);
Route::get("/bot", [\App\Http\Controllers\TelegramController::class, "homePage"]);
Route::get("/blocked", [\App\Http\Controllers\TelegramController::class, "blockedPage"])
    ->name("blocked");


Route::get('/', function () {
    return Inertia::render('Default/Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Default/Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::prefix("bot-api")
    ->middleware(["tg.auth"])
    ->group(function () {


        Route::post('/users/self', [\App\Http\Controllers\TelegramController::class, "getSelf"]);

        Route::prefix('users')
            ->middleware(["tg.role:user"])
            ->group(function () {
                // Список всех пользователей
                Route::post('/send-video', [UserController::class, 'sendVideo']);
                // Создать нового пользователя

            });
    });



