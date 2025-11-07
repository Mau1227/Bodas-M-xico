<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;

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
});