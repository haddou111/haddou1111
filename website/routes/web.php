<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Routes protégées
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    // Autres routes protégées...
});

// Routes publiques
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/about', [App\Http\Controllers\HomeController::class, 'about'])->name('about');
Route::get('/presentation', [App\Http\Controllers\HomeController::class, 'presentation'])->name('presentation');
Route::get('/mot-directeur', [App\Http\Controllers\HomeController::class, 'motDirecteur'])->name('mot-directeur');
Route::get('/recrutement', [App\Http\Controllers\HomeController::class, 'recrutement'])->name('recrutement');
Route::get('/calendrier', [App\Http\Controllers\HomeController::class, 'calendrier'])->name('calendrier');
Route::get('/administration', [App\Http\Controllers\HomeController::class, 'administration'])->name('administration');
Route::get('/emplois-du-temps', [App\Http\Controllers\EmploiDuTempsController::class, 'index'])->name('emplois-du-temps');
Route::get('/avis', [App\Http\Controllers\AvisController::class, 'index'])->name('avis');
