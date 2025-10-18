<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\PrendaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use Inertia\Inertia;

// Página principal
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// --- RUTAS GOOGLE ---
Route::get('auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'callback']);

// Catálogo público
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');

// Dashboard usuario y rutas de reservas para usuarios autenticados
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ReservaController::class, 'indexUsuario'])->name('dashboard');
    Route::get('/reservar/{prenda}', [ReservaController::class, 'create'])->name('reservas.create');
    Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
    Route::post('/reservas/{reserva}/cancelar', [ReservaController::class, 'cancelar'])->name('reservas.cancelar');

    // Rutas perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Panel admin
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/reservas', [ReservaController::class, 'indexAdmin'])->name('reservas');
        Route::get('/analisis', [AdminController::class, 'analisis'])->name('analisis');
        Route::post('/reservas/{reserva}/aprobar', [ReservaController::class, 'aprobar'])->name('reservas.aprobar');
        Route::post('/reservas/{reserva}/rechazar', [ReservaController::class, 'rechazar'])->name('reservas.rechazar');

        // CRUD prendas
        Route::resource('prendas', PrendaController::class)->names([
            'index'   => 'prendas.index',
            'store'   => 'prendas.store',
            'create'  => 'prendas.create',
            'edit'    => 'prendas.edit',
            'update'  => 'prendas.update',
            'destroy' => 'prendas.destroy',
            'show'    => 'prendas.show',
        ]);
    });

require __DIR__.'/auth.php';
