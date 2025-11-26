<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InvitationViewController;
use App\Http\Controllers\EventPhotoController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\GuestRsvpController;
use App\Http\Controllers\StatsController; // <-- este te faltaba

// =========================
// RUTAS PÚBLICAS (Marketing)
// =========================

Route::get('/', function () {
    return view('welcome');
});

// -------------------------
// RUTAS DE AUTENTICACIÓN
// -------------------------

Auth::routes();

// =========================
// RUTAS PROTEGIDAS (Dashboard)
// =========================

Route::middleware(['auth'])->group(function () {

    // Dashboard principal
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // EVENTOS
    Route::get('/evento/crear', [EventController::class, 'create'])->name('evento.create');
    Route::post('/evento', [EventController::class, 'store'])->name('evento.store');
    Route::get('/evento/{event}/editar', [EventController::class, 'edit'])->name('evento.edit');
    Route::put('/evento/{event}', [EventController::class, 'update'])->name('evento.update');
    Route::delete('/evento/{event}', [EventController::class, 'destroy'])->name('evento.destroy');

    // FOTOS
    Route::post('/evento/{event}/fotos', [EventPhotoController::class, 'store'])->name('photo.store');
    Route::delete('/fotos/{photo}', [EventPhotoController::class, 'destroy'])->name('photo.destroy');

    // ITINERARIO
    Route::post('/evento/{event}/itinerario', [ItineraryController::class, 'store'])->name('itinerary.store');
    Route::delete('/itinerario/{itinerary}', [ItineraryController::class, 'destroy'])->name('itinerary.destroy');

    // GUESTS (invitados)
    Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
    Route::post('/guests', [GuestController::class, 'store'])->name('guests.store');
    Route::put('/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
    Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');

    Route::post('/guests/import', [GuestController::class, 'import'])->name('guests.import');

    Route::get('/guests/invitations', [GuestController::class, 'invitations'])->name('guests.invitations');
    Route::post('/guests/invitations/send-bulk', [GuestController::class, 'sendBulk'])->name('guests.invitations.sendBulk');
    Route::post('/guests/{guest}/send-invitation', [GuestController::class, 'sendSingle'])->name('guests.invitations.sendSingle');

    // Stats
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');

    // Template CSV (si quieres que solo el dueño logueado lo descargue)
    Route::get('/guests/template', [GuestController::class, 'template'])->name('guests.template');

    // Mensajes de Invitados
    Route::get('/messages', [App\Http\Controllers\MessagesController::class, 'index'])->name('messages.index');
});

// =========================
// RUTAS PÚBLICAS DE INVITACIÓN
// =========================

// Invitación pública por slug de evento
Route::get('/e/{event:custom_url_slug}', [InvitationViewController::class, 'show'])->name('invitation.show');

// Invitación para invitado real con token
Route::get('/{slug}/i/{token}', [GuestRsvpController::class, 'show'])->name('rsvp.show');
Route::post('/{slug}/i/{token}', [GuestRsvpController::class, 'submit'])->name('rsvp.submit');
