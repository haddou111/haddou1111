<?php

use App\Http\Controllers\API\AvisController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EmploiDuTempsController;
use App\Http\Controllers\API\UploadController;
use App\Http\Controllers\API\EvenementController;
use App\Http\Controllers\API\ContactMessageController;
use App\Http\Controllers\API\StatistiqueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes d'authentification
Route::post('/login', [AuthController::class, 'login']);

// Routes publiques pour les statistiques
Route::get('/stats', [StatistiqueController::class, 'getStats']);
Route::get('/stats/etudiants', [StatistiqueController::class, 'getStatsEtudiants']);
Route::get('/stats/professeurs', [StatistiqueController::class, 'getStatsProfesseurs']);

// Routes middleware authentification
Route::middleware('auth:sanctum')->group(function () {
    // Profil et déconnexion
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Routes protégées pour les avis (création, mise à jour, suppression)
    Route::post('avis', [AvisController::class, 'store']);
    Route::put('avis/{id}', [AvisController::class, 'update']);
    Route::delete('avis/{id}', [AvisController::class, 'destroy']);
    
    // Routes protégées pour les emplois du temps (création, mise à jour, suppression)
    Route::post('emplois-du-temps', [EmploiDuTempsController::class, 'store']);
    Route::put('emplois-du-temps/{id}', [EmploiDuTempsController::class, 'update']);
    Route::delete('emplois-du-temps/{id}', [EmploiDuTempsController::class, 'destroy']);
    
    // Route pour télécharger des images
    Route::post('upload/image', [UploadController::class, 'uploadImage']);
    
    // Routes pour les événements
    Route::post('evenements', [EvenementController::class, 'store']);
    Route::put('evenements/{id}', [EvenementController::class, 'update']);
    Route::delete('evenements/{id}', [EvenementController::class, 'destroy']);
    
    // Routes pour les messages de contact
    Route::get('contact/messages', [ContactMessageController::class, 'index']);
    Route::get('contact/messages/{id}', [ContactMessageController::class, 'show']);
    Route::delete('contact/messages/{id}', [ContactMessageController::class, 'destroy']);
});

// Routes publiques pour les avis (lecture seulement)
Route::get('avis', [AvisController::class, 'index']);
Route::get('avis/{id}', [AvisController::class, 'show']);
Route::get('avis-recents', [AvisController::class, 'recent']);

// Routes publiques pour les emplois du temps (lecture seulement)
Route::get('emplois-du-temps', [EmploiDuTempsController::class, 'index']);
Route::get('emplois-du-temps/{id}', [EmploiDuTempsController::class, 'show']);
Route::get('filieres/{filiere_id}/emplois-du-temps', [EmploiDuTempsController::class, 'getByFiliere']);

// Routes publiques pour les événements (lecture seulement)
Route::get('evenements', [EvenementController::class, 'index']);
Route::get('evenements/{id}', [EvenementController::class, 'show']);

// Routes pour les messages de contact
Route::get('/contact', [ContactMessageController::class, 'index'])->middleware('auth:sanctum');
Route::post('/contact', [ContactMessageController::class, 'store']);
Route::get('/contact/{id}', [ContactMessageController::class, 'show'])->middleware('auth:sanctum');
Route::put('/contact/{id}/read', [ContactMessageController::class, 'markAsRead'])->middleware('auth:sanctum');
Route::delete('/contact/{id}', [ContactMessageController::class, 'destroy'])->middleware('auth:sanctum');

