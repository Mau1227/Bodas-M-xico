<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InvitationViewController; 
use App\Http\Controllers\EventPhotoController;
use App\Http\Controllers\ItineraryController;

// --- RUTAS PÚBLICAS (Marketing) ---

// Cuando alguien visite la URL raíz "/", muéstrale la vista "welcome"
Route::get('/', function () {
    return view('welcome');
});

// --- RUTAS DE AUTENTICACIÓN ---
// Esto crea las rutas de /login, /register, /logout
Auth::routes();

// --- RUTAS DEL DASHBOARD (Protegidas) ---
Route::middleware(['auth'])->group(function () {
    
    // Ruta del Dashboard principal
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // --- NUEVAS RUTAS PARA EVENTOS ---

    // 1. Ruta para MOSTRAR el formulario de creación
    // (Ej: /evento/crear)
    Route::get('/evento/crear', [EventController::class, 'create'])->name('evento.create');

    // 2. Ruta para PROCESAR Y GUARDAR el formulario
    // (Ej: /evento)
    Route::post('/evento', [EventController::class, 'store'])->name('evento.store');
    Route::get('/evento/{event}/editar', [EventController::class, 'edit'])->name('evento.edit');
    Route::put('/evento/{event}', [EventController::class, 'update'])->name('evento.update');
    Route::delete('/evento/{event}', [EventController::class, 'destroy'])->name('evento.destroy');

    // Ruta para guardar nuevas fotos de galería
    Route::post('/evento/{event}/fotos', [EventPhotoController::class, 'store'])->name('photo.store');
    // Ruta para eliminar una foto
    Route::delete('/fotos/{photo}', [EventPhotoController::class, 'destroy'])->name('photo.destroy');

    // Ruta para guardar un nuevo item del itinerario
    Route::post('/evento/{event}/itinerario', [ItineraryController::class, 'store'])->name('itinerary.store');
    // Ruta para eliminar un item
    Route::delete('/itinerario/{itinerary}', [ItineraryController::class, 'destroy'])->name('itinerary.destroy');
});

// --- RUTAS PÚBLICAS (Marketing) ---
Route::get('/', function () {
    return view('welcome');
});

// --- RUTA DE LA INVITACIÓN PÚBLICA ---
// Esta es la URL que verán los invitados (ej. /e/boda-mauroyandy)
Route::get('/e/{slug}', [InvitationViewController::class, 'show'])->name('invitation.show');


// --- RUTAS DE AUTENTICACIÓN ---
Auth::routes();

// --- RUTAS DEL DASHBOARD (Protegidas) ---
Route::middleware(['auth'])->group(function () {
    // ... (tus rutas de 'home' y 'evento.*' van aquí)
});

